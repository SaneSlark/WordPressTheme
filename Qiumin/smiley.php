<!-- 评论表情组件：点击表情时把对应短代码插入评论框。 -->
<script>
    function grin(tag) {
    	// 找到评论输入框，找不到时直接退出，避免脚本报错。
    	var myField, sel;
    	tag = ' ' + tag + ' ';
        if (document.getElementById('comment') && document.getElementById('comment').type == 'textarea') {
    		myField = document.getElementById('comment');
    	} else {
    		return false;
    	}
    	if (document.selection) {
    		myField.focus();
    		sel = document.selection.createRange();
    		sel.text = tag;
    		myField.focus();
    	}
    	else if (myField.selectionStart || myField.selectionStart == '0') {
    		var startPos = myField.selectionStart;
    		var endPos = myField.selectionEnd;
    		var cursorPos = endPos;
    		myField.value = myField.value.substring(0, startPos)
    					  + tag
    					  + myField.value.substring(endPos, myField.value.length);
    		cursorPos += tag.length;
    		myField.focus();
    		myField.selectionStart = cursorPos;
    		myField.selectionEnd = cursorPos;
    	}
    	else {
    		myField.value += tag;
    		myField.focus();
    	}
    }
</script>
<?php $smilies = '
<!-- 表情面板 HTML：由 comments.php 引入后附加到评论框下方。 -->
<a href="javascript:grin(\':?:\')"><img src="'.esc_url( get_template_directory_uri() ).'/smilies/icon_question.gif" alt="" /></a>
<a href="javascript:grin(\':razz:\')"><img src="'.esc_url( get_template_directory_uri() ).'/smilies/icon_razz.gif" alt="" /></a>
<a href="javascript:grin(\':sad:\')"><img src="'.esc_url( get_template_directory_uri() ).'/smilies/icon_sad.gif" alt="" /></a>
<a href="javascript:grin(\':evil:\')"><img src="'.esc_url( get_template_directory_uri() ).'/smilies/icon_evil.gif" alt="" /></a>
<a href="javascript:grin(\':!:\')"><img src="'.esc_url( get_template_directory_uri() ).'/smilies/icon_exclaim.gif" alt="" /></a>
<a href="javascript:grin(\':smile:\')"><img src="'.esc_url( get_template_directory_uri() ).'/smilies/icon_smile.gif" alt="" /></a>
<a href="javascript:grin(\':oops:\')"><img src="'.esc_url( get_template_directory_uri() ).'/smilies/icon_redface.gif" alt="" /></a>
<a href="javascript:grin(\':grin:\')"><img src="'.esc_url( get_template_directory_uri() ).'/smilies/icon_biggrin.gif" alt="" /></a>
<a href="javascript:grin(\':eek:\')"><img src="'.esc_url( get_template_directory_uri() ).'/smilies/icon_surprised.gif" alt="" /></a>
<a href="javascript:grin(\':shock:\')"><img src="'.esc_url( get_template_directory_uri() ).'/smilies/icon_eek.gif" alt="" /></a>
<a href="javascript:grin(\':???:\')"><img src="'.esc_url( get_template_directory_uri() ).'/smilies/icon_confused.gif" alt="" /></a>
<a href="javascript:grin(\':cool:\')"><img src="'.esc_url( get_template_directory_uri() ).'/smilies/icon_cool.gif" alt="" /></a>
<a href="javascript:grin(\':lol:\')"><img src="'.esc_url( get_template_directory_uri() ).'/smilies/icon_lol.gif" alt="" /></a>
<a href="javascript:grin(\':mad:\')"><img src="'.esc_url( get_template_directory_uri() ).'/smilies/icon_mad.gif" alt="" /></a>
<a href="javascript:grin(\':twisted:\')"><img src="'.esc_url( get_template_directory_uri() ).'/smilies/icon_twisted.gif" alt="" /></a>
<a href="javascript:grin(\':roll:\')"><img src="'.esc_url( get_template_directory_uri() ).'/smilies/icon_rolleyes.gif" alt="" /></a>
<a href="javascript:grin(\':wink:\')"><img src="'.esc_url( get_template_directory_uri() ).'/smilies/icon_wink.gif" alt="" /></a>
<a href="javascript:grin(\':idea:\')"><img src="'.esc_url( get_template_directory_uri() ).'/smilies/icon_idea.gif" alt="" /></a>
<a href="javascript:grin(\':arrow:\')"><img src="'.esc_url( get_template_directory_uri() ).'/smilies/icon_arrow.gif" alt="" /></a>
<a href="javascript:grin(\':neutral:\')"><img src="'.esc_url( get_template_directory_uri() ).'/smilies/icon_neutral.gif" alt="" /></a>
<a href="javascript:grin(\':cry:\')"><img src="'.esc_url( get_template_directory_uri() ).'/smilies/icon_cry.gif" alt="" /></a>
<a href="javascript:grin(\':mrgreen:\')"><img src="'.esc_url( get_template_directory_uri() ).'/smilies/icon_mrgreen.gif" alt="" /></a>
<br />'
?>
