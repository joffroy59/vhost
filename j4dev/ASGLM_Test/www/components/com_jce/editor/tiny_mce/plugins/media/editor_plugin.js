/* jce - 2.8.11 | 2020-05-01 | https://www.joomlacontenteditor.net | Copyright (C) 2006 - 2020 Ryan Demmer. All rights reserved | GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html */
!function(){var each=tinymce.each,extend=tinymce.extend,JSON=tinymce.util.JSON,Node=tinymce.html.Node,VK=tinymce.VK,Styles=new tinymce.html.Styles,mediaTypes={flash:{classid:"CLSID:D27CDB6E-AE6D-11CF-96B8-444553540000",type:"application/x-shockwave-flash",codebase:"http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=10,1,53,64"},shockwave:{classid:"CLSID:166B1BCA-3F9C-11CF-8075-444553540000",type:"application/x-director",codebase:"http://download.macromedia.com/pub/shockwave/cabs/director/sw.cab#version=10,2,0,023"},windowsmedia:{classid:"CLSID:6BF52A52-394A-11D3-B153-00C04F79FAA6",type:"application/x-mplayer2",codebase:"http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=10,00,00,3646"},quicktime:{classid:"CLSID:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B",type:"video/quicktime",codebase:"http://www.apple.com/qtactivex/qtplugin.cab#version=7,3,0,0"},divx:{classid:"CLSID:67DABFBF-D0AB-41FA-9C46-CC0F21721616",type:"video/divx",codebase:"http://go.divx.com/plugin/DivXBrowserPlugin.cab"},realmedia:{classid:"CLSID:CFCDAA03-8BE4-11CF-B84B-0020AFBBCCFA",type:"audio/x-pn-realaudio-plugin"},java:{classid:"CLSID:8AD9C840-044E-11D1-B3E9-00805F499D93",type:"application/x-java-applet",codebase:"http://java.sun.com/products/plugin/autodl/jinstall-1_5_0-windows-i586.cab#Version=1,5,0,0"},silverlight:{classid:"CLSID:DFEAF541-F3E1-4C24-ACAC-99C30715084A",type:"application/x-silverlight-2"},video:{type:"video/mpeg"},audio:{type:"audio/mpeg"},iframe:{}};tinymce.create("tinymce.plugins.MediaPlugin",{init:function(ed,url){function previewToImage(preview){var ifr=new tinymce.html.DomParser({},ed.schema).parse(preview.innerHTML),node=ifr.firstChild,attribs={},data=self.createTemplate(node);each(["id","lang","dir","tabindex","xml:lang","style","title","width","height","class"],function(at){attribs[at]=node.attr(at),delete data[at]});var o={iframe:data};attribs=tinymce.extend(attribs,{src:"data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7","data-mce-json":JSON.serialize(o),"data-mce-type":"iframe"}),each(["width","height"],function(at){attribs[at]=node.attr(at),attribs["data-mce-"+at]=attribs[at]});for(key in node.attributes.map)key.indexOf("data-mce-")!==-1&&(attribs[key]=n.attributes.map[key]);var placeholder=ed.dom.create("img",attribs);ed.dom.setStyles(placeholder,{width:attribs.width,height:attribs.height});var sib=preview.nextSibling;return sib&&"BR"===sib.nodeName&&sib.getAttribute("data-mce-bogus")&&ed.dom.remove(sib),ed.dom.addClass(placeholder,"mce-item-media mce-item-iframe"),ed.dom.replace(placeholder,preview),placeholder}function imageToPreview(img){ed.selection.select(img);var clone=ed.dom.clone(img);ed.dom.setStyles(clone,{width:"",height:""}),ed.dom.removeClass(clone,"mce-item-media"),ed.dom.removeClass(clone,"mce-item-iframe");for(var json=JSON.parse(clone.getAttribute("data-mce-json")),data=json.iframe||{},attribs=ed.dom.getAttribs(clone),i=0;i<attribs.length;i++){var name=attribs[i].nodeName;if("src"!==name&&name.indexOf("data-mce-")===-1){var value=ed.dom.getAttrib(clone,name);""!==value&&(data[name]=value)}}each(["width","height"],function(key){data[key]=clone.getAttribute("data-mce-"+key)||clone.getAttribute(key)}),ed.execCommand("insertMediaHtml",!1,{name:"iframe",data:data})}function isMediaNode(n){return n&&(ed.dom.is(n,".mce-item-media, .mce-item-shim")||null!==ed.dom.getParent(n,".mce-item-media"))}function isIframeMedia(n){return isMediaNode(n)&&null!==ed.dom.getParent(n,".mce-item-iframe")}function isMediaClass(cls){return cls&&/mce-item-(media|flash|shockwave|windowsmedia|quicktime|realmedia|divx|silverlight|audio|video|generic|iframe)/.test(cls)}function updatePreviewSelection(ed){var nodes=ed.dom.select(".mce-item-preview",ed.getBody());if(nodes.length){var node=nodes[nodes.length-1];node&&ed.dom.isBlock(node.parentNode)&&!node.previousSibling&&!node.nextSibling&&ed.dom.insertAfter(ed.dom.create("br",{"data-mce-bogus":1}),node)}}var self=this,lookup={},cbase={flash:{codebase:"http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version="+ed.getParam("media_version_flash","10,1,53,64")},shockwave:{codebase:"http://download.macromedia.com/pub/shockwave/cabs/director/sw.cab#version="+ed.getParam("media_version_shockwave","10,2,0,023")},windowsmedia:{codebase:"http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version="+ed.getParam("media_version_windowsmedia","10,00,00,3646")},quicktime:{codebase:"http://www.apple.com/qtactivex/qtplugin.cab#version="+ed.getParam("media_version_quicktime","7,3,0,0")},java:{codebase:"http://java.sun.com/products/plugin/autodl/jinstall-1_5_0-windows-i586.cab#Version="+ed.getParam("media_version_java","1,5,0,0")}};each(cbase,function(v,k){extend(mediaTypes[k],v)}),this.mimes={},function(data){var i,y,ext,items=data.split(/,/);for(i=0;i<items.length;i+=2)for(ext=items[i+1].split(/ /),y=0;y<ext.length;y++)self.mimes[ext[y]]=items[i]}("application/x-director,dcr,video/divx,divx,application/pdf,pdf,application/x-shockwave-flash,swf swfl,audio/mpeg,mpga mpega mp2 mp3,audio/ogg,ogg spx oga,audio/x-wav,wav,video/mpeg,mpeg mpg mpe,video/mp4,mp4 m4v,video/ogg,ogg ogv,video/webm,webm,video/quicktime,qt mov,video/x-flv,flv,video/vnd.rn-realvideo,rv","NaNvideo/x-matroska,mkv"),self.editor=ed,self.url=url,each(mediaTypes,function(v,k){v.name=k,v.classid&&(lookup[v.classid]=v),v.type&&(lookup[v.type]=v),lookup["mce-item-"+k]=v,lookup[k.toLowerCase()]=v}),self.lookup=lookup,ed.onPreInit.add(function(){var invalid=ed.settings.invalid_elements;!ed.settings.forced_root_block,"html4"===ed.settings.schema&&(ed.schema.addValidElements("iframe[longdesc|name|src|frameborder|marginwidth|marginheight|scrolling|align|width|height|allowfullscreen|seamless|*]"),ed.schema.addValidElements("video[src|autobuffer|autoplay|loop|controls|width|height|poster|*],audio[src|autobuffer|autoplay|loop|controls|*],source[src|type|media|*],embed[src|type|width|height|*]")),ed.schema.addCustomElements("mce-comment"),invalid=tinymce.explode(invalid,","),ed.parser.addNodeFilter("object,embed,video,audio,iframe",function(nodes){for(var node,i=nodes.length;i--;)node=nodes[i],cls=node.attr("class")||"",tinymce.inArray(invalid,node.name)==-1?self.toImage(node):node.remove()}),ed.serializer.addNodeFilter("img,span,div",function(nodes,name,args){for(var node,cls,i=nodes.length;i--;)node=nodes[i],cls=node.attr("class")||"",isMediaClass(cls)&&self.restoreElement(node,args)})}),ed.onInit.add(function(){var settings=ed.settings;ed.theme&&ed.theme.onResolveName&&ed.theme.onResolveName.add(function(theme,o){var n=o.node;if(n){var cls=ed.dom.getAttrib(n,"class","");cls.indexOf("mce-item-media")!==-1&&(o.name="media"),cls.indexOf("mce-item-iframe")!==-1&&(o.name="iframe")}}),ed.settings.compress.css||ed.dom.loadCSS(url+"/css/content.css"),ed.onObjectResized.add(function(ed,elm,width,height){isMediaNode(elm)&&(ed.dom.setAttrib(elm,"data-mce-width",width),ed.dom.setAttrib(elm,"data-mce-height",height),ed.dom.removeAttrib(elm,"width"),ed.dom.removeAttrib(elm,"height"),ed.dom.setStyles(elm,{width:width,height:height}))}),ed.dom.bind(ed.getDoc(),"keyup click",function(e){var node=e.target,sel=ed.selection.getNode();if(ed.dom.removeClass(ed.dom.select(".mce-item-selected.mce-item-preview"),"mce-item-selected"),node===ed.getBody()&&isMediaNode(sel))return void(sel.parentNode!==node||sel.nextSibling||ed.dom.insertAfter(ed.dom.create("br",{"data-mce-bogus":1}),sel));if(isIframeMedia(node)){e.preventDefault(),e.stopImmediatePropagation();var preview=ed.dom.getParent(node,".mce-item-media.mce-item-preview");if("click"===e.type&&VK.metaKeyPressed(e)){if("IMG"===node.nodeName)return imageToPreview(node);preview&&(preview=previewToImage(preview))}preview&&(ed.selection.select(preview),window.setTimeout(function(){ed.dom.addClass(preview,"mce-item-selected")},10)),e.preventDefault()}}),ed.onBeforeExecCommand.add(function(ed,cmd,ui,v,o){if(cmd&&cmd.indexOf("Format")!==-1){var node=ed.selection.getNode();node&&ed.dom.hasClass(node,"mce-item-preview")&&ed.selection.select(node.firstChild)}}),ed.selection.onBeforeSetContent.add(function(ed,o){settings.media_live_embed&&(o.content=o.content.replace(/<br data-mce-caret="1"[^>]+>/gi,""),/^<iframe([^>]+)><\/iframe>$/.test(o.content)&&(o.content+='<br data-mce-caret="1" />'))})}),ed.onKeyDown.add(function(ed,e){var node=ed.selection.getNode();e.keyCode!==VK.BACKSPACE&&e.keyCode!==VK.DELETE||node&&(node===ed.getBody()&&(node=e.target),node.className.indexOf("mce-item-shim")!==-1&&(node=node.parentNode),node.className.indexOf("mce-item-preview")!==-1&&ed.dom.remove(node))}),ed.onBeforeSetContent.add(function(ed,o){var h=o.content;h=h.replace(/<(audio|embed|object|video|iframe)([^>]*?)>([\w\W]+?)<\/\1>/gi,function(a,b,c,d){return d=d.replace(/<!(--)?(<!)?\[if([^\]]+)\](>--)?>/gi,"<![if$3]>"),d=d.replace(/<!\[if([^\]]+)\]>/gi,function(a,b){return'<mce-comment data-comment-condition="[if'+b+']">'}),d=d.replace(/<!(--<!)?\[endif\](--)?>/gi,"</mce-comment>"),"<"+b+c+">"+d+"</"+b+">"}),o.content=h}),ed.onPostProcess.add(function(ed,o){o.get&&(o.content=o.content.replace(/<mce-comment data-comment-condition="([^>]+)">/gi,"<!--$1>"),o.content=o.content.replace(/<\/mce-comment>/g,"<![endif]-->"))}),ed.onSetContent.add(function(ed,o){updatePreviewSelection(ed)}),tinymce.util.MediaEmbed={dataToHtml:function(name,data,innerHtml){var html="";return"iframe"===name&&(html="string"==typeof data?data:ed.dom.createHTML("iframe",data,innerHtml)),html}},ed.addCommand("insertMediaHtml",function(ui,value){var data={},name="iframe",innerHtml="";"string"==typeof value?data=value:value.name&&value.data&&(name=value.name,data=value.data,innerHtml=value.innerHtml||"");var html=tinymce.util.MediaEmbed.dataToHtml(name,data,innerHtml);ed.execCommand("mceInsertContent",!1,html,{skip_undo:1}),updatePreviewSelection(ed),ed.undoManager.add()})},convertUrl:function(url,force_absolute){var self=this,ed=self.editor,settings=ed.settings,converter=settings.url_converter,scope=settings.url_converter_scope||self;if(!url)return url;var query="",n=url.indexOf("?");return n===-1&&(url=url.replace(/&amp;/g,"&"),n=url.indexOf("&")),n>0&&(query=url.substring(n+1,url.length),url=url.substr(0,n)),url=force_absolute?ed.documentBaseURI.toAbsolute(url):converter.call(scope,url,"src","object"),url+(query?"?"+query:"")},createTemplate:function(n,o){function is_child(n){return/^(audio|embed|object|video|iframe)$/.test(n.parent.name)}var nn,hc,cn,html,v,self=this,ed=this.editor;ed.dom;if(hc=n.firstChild,nn=n.name,o=o||{},/^(audio|embed|object|param|source|video|iframe)$/.test(nn)){var at=this.serializeAttributes(n);switch(nn){case"audio":case"embed":case"object":case"video":case"iframe":case"param":hc||is_child(n)?("undefined"==typeof o[nn]&&(o[nn]={}),extend(o[nn],at),o=o[nn]):extend(o,at);break;case"source":"undefined"==typeof o[nn]&&(o[nn]=[]),o[nn].push(at)}if(hc)for(cn=n.firstChild;cn;)self.createTemplate(cn,o),cn=cn.next}else if("mce-comment"==nn)if(v=n.attr("data-comment-condition")){if("undefined"==typeof o[nn]&&(o[nn]={}),extend(o[nn],{"data-comment-condition":v}),hc)for(cn=n.firstChild,o=o[nn];cn;)self.createTemplate(cn,o),cn=cn.next}else v=new tinymce.html.Serializer({inner:!0,validate:!1}).serialize(n),"undefined"==typeof o[nn]?o[nn]=[tinymce.trim(v)]:o[nn].push(tinymce.trim(v));else html="#text"==nn?n.value:(new tinymce.html.Serializer).serialize(n);return html&&("undefined"==typeof o.html&&(o.html=[]),o.html.push(html)),o},toImage:function(n){var type,name,styles,placeholder,ed=this.editor,o={},data={},classid="";if(n.parent&&!/^(object|audio|video|embed|iframe)$/.test(n.parent.name)){placeholder=new Node("img",1),name=n.name;var style=Styles.parse(n.attr("style")),w=n.attr("width")||style.width||"",h=n.attr("height")||style.height||"",type=n.attr("type");if(data=this.createTemplate(n),"embed"==name&&"application/x-shockwave-flash"==type&&(name="object",data.param={},each(["bgcolor","flashvars","wmode","allowfullscreen","allowscriptaccess","quality"],function(k){var v=n.attr(k);if(v){if("flashvars"==k)try{v=encodeURIComponent(v)}catch(e){}data.param[k]=v}delete data[k]})),each(["audio","embed","object","video","iframe"],function(el){each(n.getAll(el),function(node){node.remove()})}),n.attr("classid")&&(classid=n.attr("classid").toUpperCase()),"object"==name){if(!data.data){var param=data.param;param&&(data.data=param.src||param.url||param.movie||param.source)}}else!data.src&&data.source&&data.source.length>1&&(data.src=data.source[0].src);var lookup=this.lookup[classid]||this.lookup[type]||this.lookup[name]||{name:"generic"};type=lookup.name||type;var style=Styles.parse(n.attr("style"));each(["bgcolor","align","border","vspace","hspace"],function(na){var v=n.attr(na);if(v)switch(na){case"bgcolor":style["background-color"]=v;break;case"align":/^(left|right)$/.test(v)?style.float=v:style["vertical-align"]=v;break;case"vspace":style["margin-top"]=v,style["margin-bottom"]=v;break;case"hspace":style["margin-left"]=v,style["margin-right"]=v;break;default:style[na]=v}}),each(["id","lang","dir","tabindex","xml:lang","style","title"],function(at){placeholder.attr(at,n.attr(at))});for(key in n.attributes.map)key.indexOf("data-mce-")!==-1&&placeholder.attr(key,n.attributes.map[key]);/^[\-0-9\.]+$/.test(w)&&(w+="px"),/^[\-0-9\.]+$/.test(h)&&(h+="px"),style.width=w,style.height=h,o[name]=data;var classes=[];if(n.attr("class")&&(classes=n.attr("class").split(" ")),name=name.toLowerCase(),classes.push("mce-item-media mce-item-"+name.toLowerCase()),type&&name!==type.toLowerCase()&&classes.push("mce-item-"+type.split("/").pop().toLowerCase()),"audio"==name){var agent=navigator.userAgent.match(/(Opera|Chrome|Safari|Gecko)/);agent&&classes.push("mce-item-agent"+agent[0].toLowerCase())}if(placeholder.attr("class",tinymce.trim(classes.join(" "))),"iframe"===name&&ed.getParam("media_live_embed",1)){var preview=new Node(name,1),attrs=o.iframe;tinymce.extend(attrs,{src:n.attr("src"),allowfullscreen:n.attr("allowfullscreen"),width:n.attr("width")||w,height:n.attr("height")||h,frameborder:"0"}),placeholder.name="span";var styles={width:w+"px",height:h+"px"};preview.attr(attrs);var msg=ed.getLang("media.preview_hint","Click to activate, %s + Click to toggle placeholder");msg=msg.replace(/%s/g,tinymce.isMac?"CMD":"CTRL"),placeholder.attr({contentEditable:"false",class:"mce-item-preview mce-item-media mce-item-"+name,"data-mce-type":"preview","aria-details":msg});var shim=new Node("span",1);shim.attr("class","mce-item-shim"),shim.attr("data-mce-bogus","1"),placeholder.append(preview),placeholder.append(shim),n.parent&&n.parent.attr("data-mce-preview")&&n.parent.unwrap()}else placeholder.attr({src:"data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"}),(styles=ed.dom.serializeStyle(style))&&placeholder.attr("style",styles),n.attr("width")&&placeholder.attr("data-mce-width",n.attr("width")),n.attr("height")&&placeholder.attr("data-mce-height",n.attr("height")),placeholder.attr({"data-mce-json":JSON.serialize(o),"data-mce-type":name});n.replace(placeholder)}},serializeAttributes:function(n){var k,v,ed=this.editor,self=this,attribs={};if("iframe"!=n||"param"!=n){var type=n.attr("type"),src=n.attr("src")||n.attr("data");if(!type&&src){var ext;/\.([a-z0-9]{2,4})/.test(src)&&(ext=/\.([a-z0-9]{2,4})/.exec(src),ext=ext[1]||""),ext&&(attribs.type=this.mimes[ext])}}if("param"==n.name){if(k=n.attr("name"),v=n.attr("value"),k&&""!=v&&"flashvars"==k)try{v=encodeURIComponent(v)}catch(e){}attribs[k]=v}else for(k in n.attributes.map)switch(v=n.attributes.map[k],k){case"poster":case"src":case"data":attribs[k]=self.convertUrl(v);break;case"autoplay":case"controls":case"loop":case"seamless":case"allowfullscreen":attribs[k]=k;break;case"frameborder":0==parseInt(v)&&"html5"===ed.settings.schema?attribs.seamless="seamless":attribs[k]=v;break;case"type":attribs[k]=v.replace(/"/g,"'");break;default:k.indexOf("data-mce-")===-1&&(attribs[k]=v)}if("embed"==n.name&&"object"==n.parent.name){var params=n.parent.getAll("param");params&&each(params,function(p){if(k=p.attr("name"),v=p.attr("value"),k&&""!=v&&"flashvars"==k)try{v=encodeURIComponent(v)}catch(e){}attribs[k]=v})}return attribs},createNodes:function(data,el){function createNode(o,el){each(o,function(v,k){var n,nn=el.name;if(tinymce.is(v,"object"))if(/(param|source)/.test(nn)&&/(audio|embed|object|video)/.test(k)&&(el=el.parent),"mce-comment"==k){var node=new Node("#comment",8);node.value=v["data-comment-condition"]+">",delete v["data-comment-condition"],el.append(node),createNode(v,el),node=new Node("#comment",8),node.value="<![endif]",el.append(node)}else if(v instanceof Array)each(v,function(s){tinymce.is(s,"string")?self.setAttribs(el,data,k,s):(node=new Node(k,1),"source"==k&&(node.shortEnded=!0),createNode(s,node),el.append(node))});else if("param"==k)for(n in v){var param=new Node(k,1);param.shortEnded=!0,self.setAttribs(param,data,n,v[n]),el.append(param)}else node=new Node(k,1),el.append(node),createNode(v,node);else if("#comment"==nn){var comment=new Node("#comment",8);comment.value=dom.decode(v),el.append(comment)}else self.setAttribs(el,data,k,v)})}var self=this,ed=this.editor,dom=ed.dom;return createNode(data,el),el},setAttribs:function(n,data,k,v){var ed=this.editor,dom=ed.dom,nn=n.name;if(null!=v&&"undefined"!=typeof v)if("param"==nn){switch(k){case"flashvars":try{v=decodeURIComponent(v)}catch(e){}break;case"src":case"movie":case"source":case"url":v=this.convertUrl(v)}n.attr("name",k),n.attr("value",v.toString())}else switch(k){case"width":case"height":v=data[k]||v,n.attr(k,v.toString());break;case"class":var cls=tinymce.explode(" ",n.attr("class"));tinymce.inArray(cls,v)===-1&&v.indexOf("mce-item-")===-1&&cls.push(tinymce.trim(v)),v=tinymce.trim(cls.join(" ")),v&&n.attr("class",v);break;case"type":n.attr(k,v.replace(/(&(quot|apos);|")/g,"'"));break;case"flashvars":try{v=decodeURIComponent(v)}catch(e){}n.attr(k,v);break;case"src":case"data":case"source":n.attr(k,this.convertUrl(v));break;case"html":var html=new Node("#text",3);html.raw=!0,html.value=(n.value?n.value:"")+dom.decode(v),n.append(html);break;default:if(!k||"undefined"==typeof v)return;n.attr(k,v.toString())}},getMimeType:function(s){if(/\.([a-z0-9]{2,4})/.test(s)){var ext=s.substring(s.length,s.lastIndexOf(".")+1).toLowerCase();return this.mimes[ext]}var props,type,cl=s.match(/mce-item-(audio|video|flash|shockwave|windowsmedia|quicktime|realmedia|divx|pdf|silverlight|iframe)/);return cl&&(props=mediaTypes[cl[1]],props&&(type=props.type)),type},restoreElement:function(n,args){var props,v;if(/mce-item-preview/.test(n.attr("class"))&&n.firstChild&&"iframe"===n.firstChild.name){var ifr=n.firstChild.clone();ifr.attr("id",n.firstChild.attr("id"));var cls=ifr.attr("class");return cls&&(cls=cls.replace(/\s?mce-item-([\w]+)/g,"").replace(/\s+/g," "),cls=tinymce.trim(cls),ifr.attr("class",cls.length>0?cls:null)),n.empty(),void n.replace(ifr)}var data=JSON.parse(n.attr("data-mce-json")),name=this.getNodeName(n.attr("class")),parent=new Node(name,1),root=data[name],src=root.src||root.data||"",params=root.param||"",style=Styles.parse(n.attr("style"));each(["width","height"],function(k){return!(v=n.attr("data-mce-"+k))||(v&&"audio"!=name&&(root[k]&&root[k]==v||(root[k]=v)),each(["object","embed","video"],function(s){root[s]&&!root[s][k]&&(root[s][k]=v)}),void delete style[k])}),each(["id","lang","dir","tabindex","xml:lang","title","class"],function(at){v=n.attr(at),"class"==at&&(v=v.replace(/\s?mce-item-([\w]+)/g,""),v=tinymce.trim(v)),v&&/\w+/.test(v)&&(root[at]=v)}),root.style=Styles.serialize(style),root.style||delete root.style;var strict=/mce-item-(flash|shockwave)/.test(n.attr("class"));if(root.type||(root.type=this.getMimeType(src)||this.getMimeType(n.attr("class"))),"object"==name)if(params=params||{},delete root.src,strict)root.data=src,/mce-item-flash/.test(n.attr("class"))&&extend(params,{movie:src}),delete params.src,delete root.embed,delete root.classid,delete root.codebase;else{var lookup=this.lookup[root.type]||this.lookup[name]||{name:"generic"};if("generic"!==lookup.name&&(root.embed||(root.embed={width:root.width,height:root.height,src:src,type:root.type}),delete root.data),root.classid||(root.classid=lookup.classid),root.codebase||(root.codebase=lookup.codebase),root.embed)for(k in params)/^(movie|source|url)$/.test(k)?root.embed.src=params[k]:root.embed[k]=params[k];if("generic"!==lookup.name){var k="src";/mce-item-windowsmedia/.test(n.attr("class"))&&(k="url"),/mce-item-silverlight/.test(n.attr("class"))&&(k="source"),params[k]=src}var props=this.lookup[name];extend(root,props),root.classid&&root.codebase&&delete root.type}else root.src&&root.source&&tinymce.is(root.source,"array")&&root.source.length&&(1==root.source.length?(root.source[0].src==root.src&&delete root.source,root.type||(root.type=this.getMimeType(root.src))):delete root.src);params&&(root.param=params);var nodes=this.createNodes(root,parent);n.replace(nodes)},getNodeName:function(s){if(s=/mce-item-(audio|embed|object|video|iframe)/i.exec(s))return s[1].toLowerCase()}}),tinymce.PluginManager.add("media",tinymce.plugins.MediaPlugin)}();