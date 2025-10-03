<?php
/**
 * 给文章代码块添加复制按钮
 * 
 * @package CodeCopyButton
 * @author CaoXin, ChatGPT
 * @version 1.3.0
 * @link https://github.com/LanternCX/Typecho-CodeCopyButton
 */
class CopyCode_Plugin implements Typecho_Plugin_Interface
{
    public static function activate()
    {
        Typecho_Plugin::factory('Widget_Archive')->footer = array('CodeCopyButton_Plugin', 'footer');
    }

    public static function deactivate(){}

    public static function config(Typecho_Widget_Helper_Form $form){}

    public static function personalConfig(Typecho_Widget_Helper_Form $form){}

    public static function footer()
    {
        echo <<<EOT
<style>
pre {
    position: relative;
    overflow: auto;
}
.copy-btn {
    position: absolute;
    top: 8px;
    right: 8px;
    background: #e0e0e0;
    color: #333;
    border: none;
    padding: 4px 8px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 12px;
    opacity: 0;
    visibility: hidden;
    transform: translateY(-4px);
    transition: opacity 180ms ease, transform 180ms ease;
    z-index: 20;
}
pre:hover .copy-btn,
pre:focus-within .copy-btn {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}
.copy-btn:hover {
    background: #bdbdbd;
}
.copy-btn:focus {
    outline: none;
    box-shadow: 0 0 0 2px rgba(0,0,0,0.06);
}
</style>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const codeBlocks = document.querySelectorAll("pre code");
    codeBlocks.forEach(function(codeBlock) {
        const pre = codeBlock.closest('pre') || codeBlock.parentNode;
        if (!pre || pre.querySelector('.copy-btn')) return;

        const button = document.createElement("button");
        button.type = 'button';
        button.innerText = "Copy";
        button.className = "copy-btn";
        button.setAttribute('aria-label','Copy Code');

        button.addEventListener("click", function(e) {
            e.stopPropagation();
            const text = codeBlock.innerText;
            const old = button.innerText;

            function success() {
                button.innerText = "Copied";
                setTimeout(() => {
                    button.innerText = old;
                }, 1500);
            }

            function fail() {
                button.innerText = "Failed";
                setTimeout(() => {
                    button.innerText = old;
                }, 1500);
            }

            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(text).then(success).catch(fail);
            } else {
                try {
                    const ta = document.createElement("textarea");
                    ta.value = text;
                    ta.style.position = 'fixed';
                    ta.style.left = '-9999px';
                    document.body.appendChild(ta);
                    ta.select();
                    document.execCommand('copy');
                    document.body.removeChild(ta);
                    success();
                } catch (err) {
                    fail();
                }
            }
        });

        pre.appendChild(button);
    });
});
</script>
EOT;
    }
}
