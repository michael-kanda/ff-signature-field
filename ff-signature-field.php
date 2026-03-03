<?php
/*
Plugin Name: FF Signature Field
Description: Digitales Unterschrift-Feld fuer Fluent Forms.
Version: 2.0.0
*/
if (!defined('ABSPATH')) exit;

add_action('plugins_loaded', function(){
if (!defined('FLUENTFORM')) { add_action('admin_notices', function(){ echo '<div class="notice notice-error"><p><b>FF Signature</b> braucht Fluent Forms.</p></div>'; }); return; }

add_filter('fluentform/editor_components', function($c){
$c['advanced'][]=['index'=>20,'element'=>'signature',
'attributes'=>['name'=>'signature','class'=>'','value'=>'','type'=>'signature'],
'settings'=>['container_class'=>'','label'=>'Unterschrift','admin_field_label'=>'Unterschrift','label_placement'=>'','help_message'=>'',
'validation_rules'=>['required'=>['value'=>false,'message'=>'Bitte unterschreiben']],'conditional_logics'=>[]],
'editor_options'=>['title'=>'Unterschrift','icon_class'=>'ff-edit-textarea','template'=>'signature']];return $c;});

add_filter('fluentform/editor_element_search_tags', function($t){$t['signature']=['signature','unterschrift','sign'];return $t;});

add_filter('fluentform/editor_element_settings_placement', function($p){
$p['signature']=['general'=>['label','admin_field_label','label_placement','validation_rules'],'advanced'=>['container_class','help_message','name','conditional_logics']];return $p;});

add_action('fluentform/render_item_signature', function($data, $form){
$el=$data['element'];
$data=apply_filters('fluentform/rendering_field_data_'.$el,$data,$form);
$s=$data['settings'];$fn=$data['attributes']['name'];
$req=!empty($s['validation_rules']['required']['value']);
$hc=!empty($s['conditional_logics']['status'])?'has-conditions':'';
$cc=trim('ff-el-group '.($s['container_class']??'').' '.$hc);
$lbl='';
if(!empty($s['label'])){
$rm=$req?'<span class="ff-el-required">*</span>':'';
$lbl='<div class="ff-el-label"><label>'.esc_html($s['label']).' '.$rm.'</label></div>';}
$hp='';
if(!empty($s['help_message']))
$hp='<div class="ff-el-help-message">'.wp_kses_post($s['help_message']).'</div>';
$uid='ffsig'.wp_rand(10000,99999);
echo '<div class="'.esc_attr($cc).'" data-name="'.esc_attr($fn).'">'.$lbl.'
<div class="ff-el-input--content">
<div id="'.$uid.'w" style="position:relative;border:1px solid #dadbdd;border-radius:6px;overflow:hidden;background:#fff">
<canvas id="'.$uid.'c" style="width:100%;height:200px;display:block;cursor:crosshair;touch-action:none"></canvas>
<div style="position:absolute;bottom:40px;left:20px;right:20px;border-bottom:1px dashed #ccc;pointer-events:none"></div>
<div style="position:absolute;bottom:14px;left:20px;font-size:11px;color:#aaa;pointer-events:none">Hier unterschreiben</div>
<button type="button" id="'.$uid.'x" style="position:absolute;top:6px;right:6px;background:rgba(0,0,0,.06);border:none;border-radius:4px;padding:4px 12px;cursor:pointer;font-size:12px;color:#555;z-index:10">&#10005; L&ouml;schen</button>
</div>
<input type="hidden" name="'.esc_attr($fn).'" id="'.$uid.'i" value="">
'.$hp.'</div></div>
<script>
(function(){
var c=document.getElementById("'.$uid.'c"),
    inp=document.getElementById("'.$uid.'i"),
    clr=document.getElementById("'.$uid.'x");
if(!c||!inp)return;
var ctx,dr=false,dn=false;
function setup(){
 var r=c.parentElement.getBoundingClientRect(),
     dpr=window.devicePixelRatio||1,
     w=r.width>0?r.width:600;
 c.width=w*dpr;c.height=200*dpr;
 c.style.width=w+"px";c.style.height="200px";
 ctx=c.getContext("2d");
 ctx.scale(dpr,dpr);
 ctx.fillStyle="#fff";ctx.fillRect(0,0,w,200);
 ctx.strokeStyle="#000";ctx.lineWidth=2;
 ctx.lineCap="round";ctx.lineJoin="round";
}
function gp(e){
 var r=c.getBoundingClientRect(),x,y;
 if(e.touches&&e.touches.length>0){x=e.touches[0].clientX;y=e.touches[0].clientY}
 else{x=e.clientX;y=e.clientY}
 return{x:x-r.left,y:y-r.top}
}
function sd(e){e.preventDefault();dr=true;var p=gp(e);ctx.beginPath();ctx.moveTo(p.x,p.y)}
function sm(e){if(!dr)return;e.preventDefault();var p=gp(e);ctx.lineTo(p.x,p.y);ctx.stroke();ctx.beginPath();ctx.moveTo(p.x,p.y);dn=true}
function su(){if(!dr)return;dr=false;ctx.beginPath();if(dn){inp.value=c.toDataURL("image/png");if(window.jQuery)jQuery(inp).trigger("change")}}
if(window.PointerEvent){
 c.addEventListener("pointerdown",sd);c.addEventListener("pointermove",sm);
 c.addEventListener("pointerup",su);c.addEventListener("pointerleave",su)
}else{
 c.addEventListener("mousedown",sd);c.addEventListener("mousemove",sm);
 c.addEventListener("mouseup",su);c.addEventListener("mouseleave",su);
 c.addEventListener("touchstart",sd,{passive:false});
 c.addEventListener("touchmove",sm,{passive:false});
 c.addEventListener("touchend",su,{passive:false})
}
if(clr)clr.addEventListener("click",function(e){e.preventDefault();e.stopPropagation();dn=false;inp.value="";setup();if(window.jQuery)jQuery(inp).trigger("change")});
setup();
var rt;window.addEventListener("resize",function(){clearTimeout(rt);rt=setTimeout(function(){dn=false;inp.value="";setup()},300)});
})();
</script>';
}, 10, 2);

add_filter('fluentform/validate_input_item_signature', function($err,$field,$fd,$fields,$form){
$fn=$field['attributes']['name']??'';$v=$fd[$fn]??'';
$req=!empty($field['settings']['validation_rules']['required']['value']);
if($req&&empty($v)){$m=$field['settings']['validation_rules']['required']['message']??'';$err=[$m?:'Bitte unterschreiben'];}return $err;}, 10, 5);

add_filter('fluentform/input_data_signature', function($v,$field,$fd){return $fd[$field['attributes']['name']??'']??'';}, 10, 3);

add_action('fluentform/submission_inserted', function($sid,$fd,$form){
if(!$fd||!is_array($fd))return;
$fields=json_decode($form->form_fields,true);if(!$fields)return;
$sf=[];
$findSig=function($flds,&$res)use(&$findSig){foreach($flds as $f){
if(($f['element']??'')==='signature')$res[]=$f['attributes']['name']??'';
if(isset($f['columns']))foreach($f['columns'] as $col){if(isset($col['fields']))$findSig($col['fields'],$res);}
if(isset($f['fields']))$findSig($f['fields'],$res);}};
$findSig($fields,$sf);if(empty($sf))return;
$ud=wp_upload_dir();$dir=$ud['basedir'].'/ff-signatures/'.$sid;wp_mkdir_p($dir);
foreach($sf as $fn){if(empty($fd[$fn]))continue;$b=$fd[$fn];
if(strpos($b,'data:image/png;base64,')!==0)continue;
$img=base64_decode(str_replace('data:image/png;base64,','',$b));if(!$img)continue;
$file=sanitize_file_name($fn.'-'.time().'.png');
file_put_contents($dir.'/'.$file,$img);
$url=$ud['baseurl'].'/ff-signatures/'.$sid.'/'.$file;
global $wpdb;$t=$wpdb->prefix.'fluentform_submissions';
$row=$wpdb->get_row($wpdb->prepare("SELECT response FROM {$t} WHERE id=%d",$sid));
if($row){$r=json_decode($row->response,true);
if($r&&isset($r[$fn])){$r[$fn]=$url;$wpdb->update($t,['response'=>wp_json_encode($r)],['id'=>$sid]);}}}
}, 10, 3);
});
