/*

@license
dhtmlxScheduler v.5.3.10 Professional

This software is covered by DHTMLX Commercial License. Usage without proper license is prohibited.

(c) XB Software Ltd.

*/
Scheduler.plugin(function(e){e.form_blocks.combo={render:function(e){e.cached_options||(e.cached_options={});var t="";return t+="<div class='"+e.type+"' style='height:"+(e.height||20)+"px;' ></div>"},set_value:function(t,a,i,n){!function(){function a(){if(t._combo&&t._combo.DOMParent){var e=t._combo;e.unload?e.unload():e.destructor&&e.destructor(),e.DOMParent=e.DOMelem=null}}a();var i=e.attachEvent("onAfterLightbox",function(){a(),e.detachEvent(i)})}(),window.dhx_globalImgPath=n.image_path||"/",
t._combo=new dhtmlXCombo(t,n.name,t.offsetWidth-8),n.onchange&&t._combo.attachEvent("onChange",n.onchange),n.options_height&&t._combo.setOptionHeight(n.options_height);var r=t._combo;if(r.enableFilteringMode(n.filtering,n.script_path||null,!!n.cache),n.script_path){var o=i[n.map_to];o?n.cached_options[o]?(r.addOption(o,n.cached_options[o]),r.disable(1),r.selectOption(0),r.disable(0)):e.$ajax.get(n.script_path+"?id="+o+"&uid="+e.uid(),function(t){var a,i=t.xmlDoc.responseText;try{
var s=JSON.parse(i);a=s.options[0].text}catch(i){var d=e.$ajax.xpath("//option",t.xmlDoc)[0];a=d.childNodes[0].nodeValue}n.cached_options[o]=a,r.addOption(o,a),r.disable(1),r.selectOption(0),r.disable(0)}):r.setComboValue("")}else{for(var s=[],d=0;d<n.options.length;d++){var _=n.options[d],l=[_.key,_.label,_.css];s.push(l)}if(r.addOption(s),i[n.map_to]){var c=r.getIndexByValue(i[n.map_to]);r.selectOption(c)}}},get_value:function(e,t,a){var i=e._combo.getSelectedValue()
;return a.script_path&&(a.cached_options[i]=e._combo.getSelectedText()),i},focus:function(e){}},e.form_blocks.radio={render:function(t){var a="";a+="<div class='dhx_cal_ltext dhx_cal_radio' style='height:"+t.height+"px;' >";for(var i=0;i<t.options.length;i++){var n=e.uid();a+="<input id='"+n+"' type='radio' name='"+t.name+"' value='"+t.options[i].key+"'><label for='"+n+"'> "+t.options[i].label+"</label>",t.vertical&&(a+="<br/>")}return a+="</div>"},set_value:function(e,t,a,i){
for(var n=e.getElementsByTagName("input"),r=0;r<n.length;r++){n[r].checked=!1;var o=a[i.map_to]||t;n[r].value==o&&(n[r].checked=!0)}},get_value:function(e,t,a){for(var i=e.getElementsByTagName("input"),n=0;n<i.length;n++)if(i[n].checked)return i[n].value},focus:function(e){}},e.form_blocks.checkbox={render:function(t){return e.config.wide_form?'<div class="dhx_cal_wide_checkbox" '+(t.height?"style='height:"+t.height+"px;'":"")+"></div>":""},set_value:function(t,a,i,n){
t=document.getElementById(n.id);var r=e.uid(),o=void 0!==n.checked_value?a==n.checked_value:!!a;t.className+=" dhx_cal_checkbox";var s="<input id='"+r+"' type='checkbox' value='true' name='"+n.name+"'"+(o?"checked='true'":"")+"'>",d="<label for='"+r+"'>"+(e.locale.labels["section_"+n.name]||n.name)+"</label>";if(e.config.wide_form?(t.innerHTML=d,t.nextSibling.innerHTML=s):t.innerHTML=s+d,n.handler){t.getElementsByTagName("input")[0].onclick=n.handler}},get_value:function(e,t,a){
e=document.getElementById(a.id);var i=e.getElementsByTagName("input")[0];return i||(i=e.nextSibling.getElementsByTagName("input")[0]),i.checked?a.checked_value||!0:a.unchecked_value||!1},focus:function(e){}}});
//# sourceMappingURL=../sources/ext/dhtmlxscheduler_editors.js.map