<?php
/**
 * ╔══════════════════════════════════════════════════════════════════╗
 *   RUKN UX PACK — تحسينات تجربة الاستخدام
 *
 *   جدول المحتويات الجانبي (Side TOC):
 *      تبويب عائم على الحافة يفتح درج محتويات ينزلق من الجانب
 *      ويقفل فيدخل تاني. الإخفاء بـ inset-inline-end مش transform
 *      حتى ما يوسعش الصفحة في RTL ويكسر الصور.
 * ╚══════════════════════════════════════════════════════════════════╝
 */

class Rukn_UX_Pack {

	public function __construct(){
		add_action( 'wp_head',   array( $this, 'ux_css' ), 98 );
		add_action( 'wp_footer', array( $this, 'ux_markup_js' ), 98 );
	}

	public function ux_css(){
		echo '<style id="rukn-ux-css">';

			echo '#rukn-toc-tab{position:fixed;top:42%;inset-inline-end:10px;z-index:860;width:46px;height:46px;border-radius:50%;background:var(--grad,linear-gradient(135deg,#014098,#1d93ff));color:#fff;border:none;cursor:pointer;display:grid;place-items:center;font-size:17px;box-shadow:0 10px 26px rgba(1,64,152,.35);transition:transform .25s,opacity .25s}';
			echo '#rukn-toc-tab:hover{transform:scale(1.08)}';
			echo '#rukn-toc-tab i{color:var(--aqua,#1d93ff)}';
			echo '#rukn-toc-tab::after{content:"جدول المحتويات";position:absolute;top:110%;inset-inline-end:50%;transform:translateX(50%);background:#014098;color:#fff;font-family:Cairo,sans-serif;font-size:11px;font-weight:700;padding:4px 10px;border-radius:8px;white-space:nowrap;opacity:0;pointer-events:none;transition:opacity .25s}';
			echo '#rukn-toc-tab:hover::after{opacity:1}';
			echo 'body.rukn-toc-open #rukn-toc-tab{opacity:0;pointer-events:none}';

			# الإخفاء بالحركة المنطقية — بدون translateX اللي كانت بتفتح الدرج وتوسّع الصفحة في RTL
			echo '#rukn-toc-panel{position:fixed;top:0;bottom:0;inset-inline-end:0;inset-inline-start:auto;height:100dvh;width:min(300px,78vw);max-width:300px;z-index:1100;background:#fff;box-shadow:-12px 0 40px rgba(1,64,152,.22);display:flex;flex-direction:column;font-family:Cairo,Tajawal,sans-serif;direction:rtl;visibility:hidden;pointer-events:none;transition:inset-inline-end .35s cubic-bezier(.2,.8,.25,1),visibility .35s}';
			echo '#rukn-toc-panel:not(.open){inset-inline-end:-110%;visibility:hidden;pointer-events:none}';
			echo '#rukn-toc-panel.open{inset-inline-end:0;visibility:visible;pointer-events:auto}';
			echo 'html[dir="rtl"] #rukn-toc-panel{box-shadow:12px 0 40px rgba(1,64,152,.22)}';
			echo '#rukn-toc-panel .rt-head{display:flex;align-items:center;gap:10px;padding:16px 18px;background:var(--grad,#014098);color:#fff;font-weight:800;font-size:15.5px;flex:none}';
			echo '#rukn-toc-panel .rt-head i{color:var(--aqua,#1d93ff)}';
			echo '#rukn-toc-panel .rt-close{margin-inline-start:auto;background:rgba(255,255,255,.15);border:none;color:#fff;width:28px;height:28px;border-radius:50%;cursor:pointer;display:grid;place-items:center}';
			echo '#rukn-toc-panel .rt-search{padding:12px 14px;border-bottom:1px solid #eef2f6;flex:none}';
			echo '#rukn-toc-panel .rt-search input{width:100%;border:1.5px solid #e3e9f2;border-radius:10px;padding:9px 12px;font-family:Tajawal,sans-serif;font-size:14px;outline:none}';
			echo '#rukn-toc-panel .rt-search input:focus{border-color:var(--blue,#1d93ff)}';
			echo '#rukn-toc-panel .rt-list{flex:1;overflow-y:auto;-webkit-overflow-scrolling:touch;padding:10px 8px}';
			echo '#rukn-toc-panel .rt-list a{display:block;padding:10px 14px;border-radius:10px;color:#33415c;font-weight:700;font-size:14px;line-height:1.6;text-decoration:none;transition:.2s}';
			echo '#rukn-toc-panel .rt-list a:hover{background:rgba(46,157,247,.08);color:var(--turq,#1FB5A3)}';
			echo '#rukn-toc-panel .rt-list a.rt-h3{padding-inline-start:30px;font-weight:400;font-size:13.5px;color:#5b6b85}';
			echo '#rukn-toc-backdrop{position:fixed;inset:0;background:rgba(1,64,152,.45);z-index:1090;opacity:0;pointer-events:none;visibility:hidden;transition:opacity .3s,visibility .3s}';
			echo '#rukn-toc-backdrop.open{opacity:1;pointer-events:auto;visibility:visible}';
			echo 'body.rukn-toc-open{overflow:hidden}';

			echo '@media (max-width:768px){';
				echo '#rukn-toc-tab{top:38%;width:44px;height:44px;inset-inline-end:8px;font-size:16px}';
				echo '#rukn-toc-tab::after{display:none}';
				echo '#rukn-toc-panel{width:min(280px,78vw)}';
			echo '}';

		echo '</style>';
	}

	public function ux_markup_js(){

		if( ! is_singular() ){
			return;
		}

		echo '<script type="text/javascript">';
			echo '(function(){';
				echo 'var content=document.querySelector(".-single-post-content")||document.querySelector(".article-body");';
				echo 'if(!content)return;';
				echo 'var heads=content.querySelectorAll("h2,h3");';
				echo 'if(heads.length<2)return;';
				echo 'if(document.getElementById("rukn-toc-panel"))return;';

				echo 'var tab=document.createElement("button");tab.id="rukn-toc-tab";tab.type="button";tab.setAttribute("aria-label","جدول المحتويات");tab.setAttribute("aria-expanded","false");tab.innerHTML=\'<i class="fas fa-list-ul"></i>\';document.body.appendChild(tab);';
				echo 'var backdrop=document.createElement("div");backdrop.id="rukn-toc-backdrop";document.body.appendChild(backdrop);';
				echo 'var panel=document.createElement("div");panel.id="rukn-toc-panel";panel.setAttribute("role","dialog");panel.setAttribute("aria-modal","true");panel.setAttribute("aria-label","جدول المحتويات");';
				echo 'panel.innerHTML=\'<div class="rt-head"><i class="fas fa-list-ul"></i> جدول المحتويات<button type="button" class="rt-close" aria-label="إغلاق">✕</button></div><div class="rt-search"><input type="search" placeholder="ابحث في العناوين..."></div><div class="rt-list"></div>\';';
				echo 'document.body.appendChild(panel);';

				echo 'var list=panel.querySelector(".rt-list");';
				echo 'heads.forEach(function(h,i){';
					echo 'if(!h.id)h.id="rukn-h-"+i;';
					echo 'var a=document.createElement("a");';
					echo 'a.href="#"+h.id;';
					echo 'a.textContent=h.textContent.trim();';
					echo 'if(h.tagName==="H3")a.className="rt-h3";';
					echo 'a.addEventListener("click",function(e){';
						echo 'e.preventDefault();close();';
						echo 'var top=h.getBoundingClientRect().top+window.pageYOffset-95;';
						echo 'window.scrollTo({top:top,behavior:"smooth"});';
					echo '});';
					echo 'list.appendChild(a);';
				echo '});';

				echo 'panel.querySelector(".rt-search input").addEventListener("input",function(){';
					echo 'var q=this.value.trim();';
					echo 'list.querySelectorAll("a").forEach(function(a){';
						echo 'a.style.display=(q===""||a.textContent.indexOf(q)>-1)?"block":"none";';
					echo '});';
				echo '});';

				echo 'function open(){panel.classList.add("open");backdrop.classList.add("open");document.body.classList.add("rukn-toc-open");tab.setAttribute("aria-expanded","true")}';
				echo 'function close(){panel.classList.remove("open");backdrop.classList.remove("open");document.body.classList.remove("rukn-toc-open");tab.setAttribute("aria-expanded","false")}';
				echo 'tab.addEventListener("click",function(){panel.classList.contains("open")?close():open()});';
				echo 'backdrop.addEventListener("click",close);';
				echo 'panel.querySelector(".rt-close").addEventListener("click",close);';
				echo 'document.addEventListener("keydown",function(e){if(e.key==="Escape")close()});';
			echo '})();';
		echo '</script>';
	}

}
new Rukn_UX_Pack;
