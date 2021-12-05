<?php
/**
 * @Copyright
 * @package        JVE - Joomla Vote Extended for Joomla! 3.x
 * @author         Viktor Vogel <admin@kubik-rubik.de>
 * @version        3.3.4 - 2019-10-12
 * @link           https://kubik-rubik.de/jve-joomla-vote-extended
 *
 * @license        GNU/GPL
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
defined('_JEXEC') || die('Restricted access');

class PlgContentJoomlaVoteExtended extends JPlugin
{
    protected $app;
    protected $autoloadLanguage = true;
    protected $db;
    protected $htmlContent = '';
    protected $viewArticle = false;

    /**
     * Plugin is executed when the trigger onContentPrepare is called to add the rating stars
     *
     * @param string    $context
     * @param object    $row
     * @param JRegistry $params
     * @param integer   $page
     *
     * @return bool
     * @throws Exception
     */
    public function onContentPrepare($context, &$row, &$params, $page = 0)
    {
        if (stripos($context, 'com_content') === false) {
            return false;
        }

        if (!($params instanceof JRegistry) || empty($params) || is_string($params)) {
            return false;
        }

        $showVote = (bool) $params->get('show_vote');

        if ($showVote === false) {
            return false;
        }

        if ($this->excludeArticleId($row->id)) {
            return false;
        }

        if ($this->app->input->getString('view', '') == 'article') {
            $this->viewArticle = true;
        }

        if ($this->params->get('articleview', 0) && !$this->viewArticle) {
            return false;
        }

        $this->renderVoteOutput($row);
    }

    /**
     * Checks the ID of the article against a black list
     *
     * @param $id
     *
     * @return bool
     */
    private function excludeArticleId($id)
    {
        $excludeArticlesIds = $this->params->get('exclude_articles_ids', '');

        if (!empty($excludeArticlesIds)) {
            $excludeArticlesIds = array_map('trim', explode(',', $excludeArticlesIds));

            if (in_array($id, $excludeArticlesIds)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Adds the HTML output for the rating stars to the article text
     *
     * @param $row
     */
    private function renderVoteOutput(&$row)
    {
        $this->createVoteOutput($row);

        if (!$this->params->get('position')) {
            $row->text = $this->htmlContent . $row->text;

            return;
        }

        $row->text .= $this->htmlContent;
    }

    /**
     * Creates the HTML code and adds JSS / CSS information
     *
     * @param $row
     *
     * @return string
     */
    private function createVoteOutput($row)
    {
        $rating = 0;
        $ratingBest = 5;
        $ratingCount = 0;
        $this->getRatingData($row, $rating, $ratingCount);

        $this->htmlContent = '<!-- JVE - Joomla Vote Extended - Kubik-Rubik Joomla! Extensions -->';
        $this->htmlContent .= '<div class="content_rating" itemprop="aggregateRating" itemscope itemtype="https://schema.org/AggregateRating"><meta itemprop="ratingValue" content="' . $rating . '" /><meta itemprop="bestRating" content="' . $ratingBest . '" /><meta itemprop="ratingCount" content="' . $ratingCount . '" /><meta itemprop="worstRating" content="0" /><meta itemprop="itemReviewed" content="' . htmlspecialchars($row->title, ENT_QUOTES, 'UTF-8') . '" />';

        $introText = $this->params->get('introtext', '');

        if ($this->viewArticle && !empty($introText)) {
            $this->htmlContent .= '<div class="jve-introtext">' . JText::_($introText) . '</div>';
        }

        $this->htmlContent .= '<div class="jve-stars"><span id="jve-' . $row->id . '"></span><span id="jve-response"></span></div>';

        if ($this->viewArticle && $this->params->get('statistics', false)) {
            $this->htmlContent .= '<div class="jve-statistics">' . JText::sprintf('PLG_JOOMLAVOTEEXTENDED_STATISTICS_FRONTEND', $rating, $ratingBest, $ratingCount) . '</div>';
        }

        $this->htmlContent .= '</div>';

        $this->loadHeadData($rating, $row->id, $row->state);
    }

    /**
     * Gets the rating information for the loaded article
     *
     * @param $row
     * @param $rating
     * @param $ratingCount
     */
    private function getRatingData($row, &$rating, &$ratingCount)
    {
        if (!$this->params->get('decimals', false)) {
            if (!empty($row->rating)) {
                $rating = (int) $row->rating;
            }

            if (!empty($row->rating_count)) {
                $ratingCount = (int) $row->rating_count;
            }

            return;
        }

        $query = $this->db->getQuery(true);
        $query->select('ROUND(rating_sum / rating_count, 2) AS rating, rating_count');
        $query->from('#__content_rating');
        $query->where('content_id = ' . (int) $row->id);
        $this->db->setQuery($query);
        $ratingData = $this->db->loadObject();

        if (!empty($ratingData->rating)) {
            $rating = $ratingData->rating;
        }

        if (!empty($ratingData->rating_count)) {
            $ratingCount = $ratingData->rating_count;
        }
    }

    /**
     * Loads correct data to the <head> section
     *
     * @param $rating
     * @param $id
     * @param $state
     */
    private function loadHeadData($rating, $id, $state)
    {
        static $loadOnce = true;
        $document = JFactory::getDocument();

        if ($loadOnce) {
            JHtml::_('jquery.framework');
            $document->addStyleSheet('plugins/content/joomlavoteextended/assets/css/star-rating-svg.css', 'text/css');
            $document->addScript('plugins/content/joomlavoteextended/assets/jss/jquery.star-rating-svg.min.js', 'text/javascript');

            if (!$this->viewArticle) {
                $document->addStyleDeclaration('.jq-star{cursor: default;}');
            }

            $loadOnce = false;
        }

        $document->addScriptDeclaration($this->createScriptData($rating, $id, $state));
    }

    /**
     * Creates the required JavaScript code
     *
     * @param $rating
     * @param $id
     * @param $state
     *
     * @return string
     */
    private function createScriptData($rating, $id, $state)
    {
        $script = 'jQuery(document).ready(function(){
					jQuery("#jve-' . $id . '").starRating({ 
						starSize: 20,
						totalStars: 5,
						useFullStars: true,
						initialRating: ' . $rating . ',
						';
        $script .= $this->createScriptDataBody($state);
        $script .= '});
				 });';

        return $script;
    }

    /**
     * Created the body of the JavaScript code
     *
     * @param $state
     *
     * @return string
     */
    private function createScriptDataBody($state)
    {
        if ($state == 1 && $this->viewArticle) {
            $responseMessage = JText::_('PLG_JOOMLAVOTEEXTENDED_ARTICLE_VOTE_SUCCESS');
            $messages = $this->app->getMessageQueue();

            if (!empty($messages)) {
                $messageArray = array();

                foreach ($messages as $message) {
                    if (!empty($message['message'])) {
                        $messageArray[] = $message['message'];
                    }
                }

                $responseMessage = implode(' ', $messageArray);
            }

            $this->htmlContent .= '<span class="jve-invisible">' . $responseMessage . '</span>';

            // Handle response properly even if cache functionality is used
            $url = htmlspecialchars(JUri::getInstance()->toString());

            return 'callback: function(currentRating, $el){ 
					        jQuery.post("' . $url . '",{ 
					            url: "' . $url . '",
			                    task: "article.vote", 
							    hitcount: "0", 
							    user_rating: currentRating,
							    "' . JSession::getFormToken() . '": 1
						    }).done(function (response){
						        var response_message = "' . $responseMessage . '";
						        var rating_output = "<span class=\"jve-message\">' . $responseMessage . '</span>";
						        var rating_response = response.match(/<span class="jve-invisible">([^<]*)<\/span>/);
								if(jQuery.isArray(rating_response) && rating_response[1])
								{
									var rating_output = "<span class=\"jve-message\">" + rating_response[1] + "</span>";
								}
			                    jQuery("#jve-response").empty().append(rating_output);
							});
				        }';
        }

        return 'readOnly: true';
    }
}
