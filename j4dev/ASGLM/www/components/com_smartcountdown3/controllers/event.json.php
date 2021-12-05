<?php
/**
 * @package Smart Countdown 3 AJAX server for Joomla! 3.0
 * @version 3.4.4
 * @author Alex Polonski
 * @copyright (C) 2012-2015 - Alex Polonski
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/
defined ( '_JEXEC' ) or die ();
class smartCountdown3ControllerEvent extends JControllerLegacy {
	public function getEvent() {
		$app = JFactory::getApplication ();
		$module_id = $app->input->getInt ( 'scd_module_id', 0 );
		
		// get requested module data
		$db = JFactory::getDbo ();
		$db->setQuery ( 'SELECT a.* FROM #__modules AS a WHERE a.id = ' . $module_id );
		try {
			$module = $db->loadObject ();
		} catch ( RuntimeException $e ) {
			self::sendResponse ( null, 500, $e->getMessage () );
			return;
		}
		// check that the module exists
		$params_raw = @$module->params;
		if (empty ( $params_raw )) {
			self::sendResponse ( null, 100, 'Module not found' );
			return;
		}
		
		$params = new JRegistry ();
		$params->loadString ( $params_raw );
		
		// look for events import plugins
		$dispatcher = JDispatcher::getInstance ();
		JPluginHelper::importPlugin ( 'system' );
		$result = $dispatcher->trigger ( 'onCountdownGetEventsQueue', array (
				'mod_smartcountdown3',
				$params 
		) );
		
		if (in_array ( false, $result, true )) {
			// this is a json controller and plugins cannot display error messages directly.
			// plugins can add messages to application queue (in debug mode, e.g.) and we can
			// extract them here and send in error response
			$messages = $app->getMessageQueue ();
			if (! empty ( $messages )) {
				$message = array ();
				foreach ( $messages as $m ) {
					$message [] = $m ['message'];
				}
				$message = implode ( ', ', $message );
			} else {
				$message = 'Error processing event import plugin';
			}
			// Do not change error code below - it is required for
			// event import plugins debugging mode
			self::sendResponse ( null, 101, $message );
			return;
		}
		
		// filter out empty result elements (plugin returned true)
		$result = array_filter ( $result, function ($v) {
			return $v !== true;
		} );
		
		$now_micro_ts = microtime ( true );
		$now_ts_millis = round ( $now_micro_ts, 3 ) * 1000;
		$now_ts = round ( $now_micro_ts );
		
		// get counter display modes from options. Here we are interested in
		// "countup limit" only.
		$modes = explode ( ':', $params->get ( 'counter_modes', '-1:-1' ) );
		
		if (empty ( $result )) {
			// plugins not enabled for this module instance or system-wide
			// get internal counter
			$deadline = JDate::getInstance ( $params->get ( 'deadline', 'now' ) );
			
			// for internal counter "countdown to end" mode (-2) has no sense
			$countup_limit = $modes [1] < 0 ? - 1 : $modes [1];
			
			if ($countup_limit >= 0 && $deadline->getTimestamp () + $countup_limit <= $now_ts) {
				$deadline = '';
			} else {
				$deadline = $deadline->format ( 'c' );
			}
			
			$options = array (
					'deadline' => $deadline,
					'countup_limit' => $countup_limit,
					'countdown_query_limit' => -1,
					'now' => $now_ts_millis 
			);
		} else {
			// process imported events
			$countup_limit = $modes [1];
			
			$current_event = self::processImportedEvents ( $result, $countup_limit, $now_ts );
			if (empty ( $current_event )) {
				$options = array (
						'deadline' => '',
						'countup_limit' => '',
						'imported_title' => '',
						'now' => $now_ts_millis 
				);
			} else {
				$options = array (
						'deadline' => $current_event ['deadline'],
						'countup_limit' => $current_event ['countup_limit'],
						'countdown_query_limit' => $current_event ['countdown_query_limit'],
						'imported_title_down' => $current_event ['imported_title_down'],
						'imported_title_up' => $current_event ['imported_title_up'],
						'is_countdown_to_end' => $current_event ['is_countdown_to_end'],
						'now' => $now_ts_millis 
				);
				// Only add redirect URLs to response if they are defined by event import plugin
				// (when not defined in response, those set in module options will be used)
				if (isset ( $current_event ['click_url'] )) {
					$options ['click_url'] = $current_event ['click_url'];
				}
				if (isset ( $current_event ['redirect_url'] )) {
					$options ['redirect_url'] = $current_event ['redirect_url'];
				}
			}
		}
		self::sendResponse ( $options );
	}
	private static function processImportedEvents($result, $countup_limit, $now_ts) {
		if (empty ( $result )) {
			return false;
		}
		
		// Plain events arrays
		$current_events = array ();
		$future_events = array ();
		
		// merge events from all providers. For now there is no difference which
		// import plugin events comes from
		foreach ( $result as $group ) {
			foreach ( $group as $i => $event ) {
				// ===== old import modules handle "countdown to end mode" creating two events with
				// duration zero, setting 'is_countdown_to_end' flag for the second event and
				// its deadline as first event deadline + first event imported duration.
				// For each group such events always go one after another in unsorted timeline,
				// so we can detect CTE simutation events pair
				if(isset($group[$i + 1]) && !empty($group[$i + 1]['is_countdown_to_end'])) {
					// if we have next event and it is CTE, modify current event setting its
					// 'is_countdown_to_end' flag and correct duration (recover it from difference
					// in deadlines)
					$event['is_countdown_to_end'] = 1;
					$event['duration'] = $group[$i + 1]['deadline'] - $group[$i]['deadline'];
					// mark next event as processed - it shouldn't be added to timeline
					$group[$i + 1]['skip_event'] = 1;
				}
				if(!empty($group[$i]['skip_event'])) {
					// this event was already processed, discard it
					continue;
				}
				// end old plugins compatibility code =====
				
				// separate and filter events
				if ($event ['deadline'] <= $now_ts) {
					// event already started
					if ($event ['duration'] >= 0) {
						$duration_filter = $countup_limit >= 0 ? min ( $countup_limit, $event ['duration'] ) : $event ['duration'];
						if ($event ['deadline'] + $duration_filter > $now_ts) {
							$current_events [] = $event;
						}
					} else {
						// we are interested in all started events which have no end date
						$current_events [] = $event;
					}
				} elseif ($event ['deadline'] > $now_ts) {
					// we are interested in all future events
					$future_events [] = $event;
				}
				// finished events are discarded
			}
		}
		
		$is_countdown_to_end = $countup_limit == - 2;
		
		// Structured events. Each deadline will be an array of events, keyed and sorted
		// by their end time (normal) or start time (CTE)
		
		$current_events = self::groupEvents ( $current_events, 'current', $is_countdown_to_end );
		$future_events = self::groupEvents ( $future_events, 'future', false );
		
		$max_countup_limit = 0;
		
		if ($is_countdown_to_end) {
			// CTE (countdown-to-end) mode
			if (! empty ( $current_events )) {
				// closest event(s) end time is the deadline
				$event_end_times = array_keys ( $current_events );
				$deadline_ts = $event_end_times [0];
				
				// get events group (for overlapping events)
				$events = reset ( $current_events );
				$event_start_times = array_keys ( $events );
				
				// if there are future events we need the closest event start to
				// set countdown limit - when this limit is reached we must repeat event query
				$countdown_query_limit = 0;
				if (! empty ( $future_events )) {
					// future events are always grouped by start dates
					$event_start_times = array_keys ( $future_events );
					$countdown_query_limit = $deadline_ts - $event_start_times [0];
					if ($countdown_query_limit < 0) {
						// if the closest future event start after the current events finish
						// we ignore the difference
						$countdown_query_limit = 0;
					}
				}
				$countdown_to_end = 1;
			} elseif (! empty ( $future_events )) {
				$event_start_times = array_keys ( $future_events );
				$deadline_ts = $event_start_times [0];
				
				$events = reset ( $future_events );
				$event_end_times = array_keys ( $events );

				// we have only future events. In CTE mode we must repeat event query once
				// the deadline is reached
				$countdown_query_limit = 0;
				$countdown_to_end = 0;
			}
		} else {
			$current_event_start_times = array_keys ( $current_events );
			$future_event_start_times = array_keys ( $future_events );
			
			// normal mode
			if (! empty ( $current_events )) {
				// most recently started event(s) start time is the deadline
				$deadline_ts = $current_event_start_times [0];
				
				// get events group (for overlapping events)
				$events = reset ( $current_events );
				$event_end_times = array_keys ( $events );
				
				if (! empty ( $future_events )) {
					// limit countup to next event start and event duration
					$max_countup_limit = min ( $future_event_start_times [0], $event_end_times [0] ) - $deadline_ts;
				} else {
					// no more events - limit countup to event duration only
					$max_countup_limit = $event_end_times [0] - $deadline_ts;
				}
			} elseif (! empty ( $future_events )) {
				// we have only future events
				// the closest future event(s) start time is the deadline
				$deadline_ts = $future_event_start_times [0];
				
				$events = reset ( $future_events );
				$event_end_times = array_keys ( $events );
				
				if (isset ( $future_event_start_times [1] )) {
					// limit countup to next event start and event duration
					$max_countup_limit = min ( $future_event_start_times [1], $event_end_times [0] ) - $deadline_ts;
				} else {
					// no more events - limit countup to event duration only
					$max_countup_limit = $event_end_times [0] - $deadline_ts;
				}
			}
			
			// adjust countup_limit
			if ($countup_limit >= 0) {
				$countup_limit = min ( $countup_limit, $max_countup_limit );
			} else {
				$countup_limit = $max_countup_limit;
			}
			// no CTE in normal mode
			$countdown_to_end = 0;
			$countdown_query_limit = - 1;
		}
		
		// normally event import plugins will fetch only valid events,
		// just in case the timeline is empty, we simulate "no events found"
		if (empty ( $events )) {
			return false;
		}
		
		// event import plugins can be set up to import event titles:
		// common titles - should be displayed both before event and when event has started
		// per-mode titles - one for countdown mode and the other for count up or countdown-to-end
		
		// we maintain 2 array for all simultaneos events. If an event imported has per-mode
		// titles set we add each title to the corresponding concatenation array,
		// otherwise (common titles) event title will be added to both concat arrays.
		$concat_title_down = array ();
		$concat_title_up = array ();
		
		$redirect_url = null;
		$click_url = null;
		
		// iterate through events - construct title proposal and detect
		// countdown_to_end events
		foreach ( $events as &$event ) {
			// update concatenation arrays
			self::concatTitles ( $concat_title_down, $concat_title_up, $event );
			
			// if there are more than 1 current evetns, each one can define its own
			// redirection URL. We must resolve this conflict for both auto-redirect and click:
			// the first event in list must win. Links in event titles will work OK even if
			// multiple events are listed.
			if (empty ( $redirect_url ) && ! empty ( $event ['redirect_url'] )) {
				$redirect_url = $event ['redirect_url'];
			}
			if (empty ( $click_url ) && ! empty ( $event ['click_url'] )) {
				$click_url = $event ['click_url'];
			}
		}
		
		// start clean data structure
		$event = array ();
		
		// join titles to a string (may be empty string if no titles found)
		$concat_title_down = implode ( ', ', $concat_title_down );
		$event ['imported_title_down'] = $concat_title_down;
		$concat_title_up = implode ( ', ', $concat_title_up );
		$event ['imported_title_up'] = $concat_title_up;
		
		$deadline = new DateTime ();
		$deadline->setTimestamp ( $deadline_ts );
		$event ['deadline'] = $deadline->format ( 'c' );
		$event ['is_countdown_to_end'] = $countdown_to_end;
		if (! empty ( $redirect_url )) {
			$event ['redirect_url'] = $redirect_url;
		}
		if (! empty ( $click_url )) {
			$event ['click_url'] = $click_url;
		}
		$event ['countup_limit'] = $countup_limit;
		$event ['countdown_query_limit'] = $countdown_query_limit;
		
		return $event;
	}
	private static function groupEvents($unsorted, $events_type, $is_countdown_to_end = false) {
		$timeline = array ();
		foreach ( $unsorted as $event ) {
			if ($is_countdown_to_end && $event ['duration'] == - 1) {
				// no countdown-to-end for events with no end date
				continue;
			}
			
			$event_start_ts = $event ['deadline'];
			if ($event ['duration'] >= 0) {
				$event_end_ts = $event ['deadline'] + $event ['duration'];
			} else {
				$event_end_ts = PHP_INT_MAX;
			}
			
			if ($is_countdown_to_end) {
				// for countdown-to-end mode group events by end date
				if (! isset ( $timeline [$event_end_ts] )) {
					$timeline [$event_end_ts] = array ();
				}
				// make sure we have unique $event_start_ts key: otherwise if there are fully overlapping
				// events the last event data will overwrite the previous one(s) which will be lost
				while ( isset ( $timeline [$event_end_ts] [$event_start_ts] ) ) {
					$event_start_ts = '0' . $event_start_ts;
				}
				// add event to timeline
				$timeline [$event_end_ts] [$event_start_ts] = $event;
			} else {
				// for normal mode group events by start date
				if (! isset ( $timeline [$event_start_ts] )) {
					$timeline [$event_start_ts] = array ();
				}
				// make sure we have unique $event_end_ts key: otherwise if there are fully overlapping
				// events the last event data will overwrite the previous one(s) which will be lost
				while ( isset ( $timeline [$event_start_ts] [$event_end_ts] ) ) {
					$event_end_ts = '0' . $event_end_ts;
				}
				// add event to timeline
				$timeline [$event_start_ts] [$event_end_ts] = $event;
			}
		}
		
		if ($is_countdown_to_end) {
			$events = self::sortEvents ( $timeline, 'asc'/*, 'asc'*/ );
		} else {
			$events = self::sortEvents ( $timeline, $events_type == 'future' ? 'asc' : 'desc' );
		}
		
		return $events;
	}
	private static function sortEvents($timeline, $sort = 'asc') {
		// Sort each group
		
		// user-defined sort function: for numerically distinct values
		// we compare numerically, for zero-padded trick values we compare string length -
		// very easy but effective - the only difference is the number of zeros prepended
		// to the value, so this simple will do the trick - the shortest (i.e. added first)
		// will come first.
		foreach ( $timeline as &$group ) {
			uksort ( $group, function ($a, $b) {
				if (intval ( $a ) == intval ( $b )) {
					return strlen ( $a ) > strlen ( $b ) ? 1 : (strlen ( $a ) < strlen ( $b ) ? - 1 : 0);
				} else {
					return intval ( $a ) > intval ( $b ) ? 1 : (intval ( $a ) < intval ( $b ) ? - 1 : 0);
				}
			} );
		}
		
		// Sort groups
		ksort ( $timeline, SORT_NUMERIC );
		if ($sort == 'desc') {
			// revert order for 'desc' sort
			$timeline = array_reverse ( $timeline, true );
		}
		
		return $timeline;
	}
	private static function concatTitles(&$concat_title_down, &$concat_title_up, $event) {
		// implicitly reduce full duplicates
		if (isset ( $event ['title_down'] ) && trim ( $event ['title_down'] ) != '') {
			$concat_title_down [$event ['title_down']] = $event ['title_down'];
		} elseif (isset ( $event ['title'] ) && trim ( $event ['title'] ) != '') {
			$concat_title_down [$event ['title']] = $event ['title'];
		}
		if (isset ( $event ['title_up'] ) && trim ( $event ['title_up'] ) != '') {
			$concat_title_up [$event ['title_up']] = $event ['title_up'];
		} elseif (isset ( $event ['title'] ) && trim ( $event ['title'] ) != '') {
			$concat_title_up [$event ['title']] = $event ['title'];
		}
	}
	private static function sendResponse($options = array('deadline' => ''), $err_code = 0, $err_msg = '') {
		$response = array (
				'err_code' => $err_code,
				'err_msg' => $err_msg,
				'options' => $options 
		);
		
		// clear output buffer to suppress warning and notices
		while ( ob_get_clean () )
			;
		
		echo json_encode ( $response );
		JFactory::getApplication ()->close ();
	}
}