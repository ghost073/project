<?php
//js缩放内容图片里的img标签
function smallImg($content,$width=600,$height=400) {
    // 替换IMG标签
    $img_patten = '/<img[^>]*src=[\'"]?([^>\'"\s]*)[\'"]?[^>]*>/i';
    $content = preg_replace($img_patten, '<img src="$1" onload="AutoResizeImage('.$width.','.$height.',this)" />', $content);
    return $content;
}
?>
<img src="1.jpg" onload="AutoResizeImage(600,1000,this)" />
<script type="text/javascript">
    function AutoResizeImage(maxWidth,maxHeight,objImg){
        var img = new Image();
        img.src = objImg.src;
        var hRatio;
        var wRatio;
        var Ratio = 1;
        var w = img.width;
        var h = img.height;
        wRatio = maxWidth / w;
        hRatio = maxHeight / h;
        if (maxWidth ==0 && maxHeight==0){
            Ratio = 1;
        }else if (maxWidth==0){//
            if (hRatio<1) Ratio = hRatio;
        }else if (maxHeight==0){
            if (wRatio<1) Ratio = wRatio;
        }else if (wRatio<1 || hRatio<1){
            Ratio = (wRatio<=hRatio?wRatio:hRatio);
        }
        if (Ratio<1){
            w = w * Ratio;
            h = h * Ratio;
        }
        objImg.height = h;
        objImg.width = w;
    }

</script>