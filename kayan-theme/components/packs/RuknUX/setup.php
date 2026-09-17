<?php
/**
 * ╔══════════════════════════════════════════════════════════════════╗
 *   RUKN UX PACK — تحسينات تجربة الاستخدام
 *
 *   جدول المحتويات الجانبي (Side TOC):
 *      سطح المكتب فقط، وعلى الصفحات اللي مفيهاش Easy TOC / Rank Math.
 *      الموبايل: الدرج العائم كان بيغطي الهيدر والهيرو (نفس علّة RTL
 *      بتاعة قائمة .mob) فيتم إخفاؤه هناك والاعتماد على محتويات المقال.
 * ╚══════════════════════════════════════════════════════════════════╝
 */

class Rukn_UX_Pack {

	public function __construct(){
		add_action( 'wp_head',   array( $this, 'ux_css' ), 98 );
		add_action( 'wp_footer', array( $this, 'ux_markup_js' ), 98 );
	}

	# ════════════════════════════════════════════════════════════════
	# CSS
	# ════════════════════════════════════════════════════════════════
	public function ux_css(){
		echo '<style id="rukn-ux-css">';

			# الزر — حافة النهاية المنطقية، تحت الهيدر
			echo '#rukn-toc-tab{position:fixed;top:42%;inset-inline-end:12px;z-index:850;width:48px;height:48px;border-radius:50%;background:var(--grad,linear-gradient(135deg,#0A1F4E,#14335e));color:#fff;border:none;cursor:pointer;display:grid;place-items:center;font-size:18px;box-shadow:0 10px 26px rgba(10,26,51,.35);transition:transform .25s}';
			echo '#rukn-toc-tab:hover{transform:scale(1.08)}';
			echo '#rukn-toc-tab i{color:var(--aqua,#4FA8FF)}';
			echo '#rukn-toc-tab::after{content:"جدول المحتويات";position:absolute;top:110%;right:50%;transform:translateX(50%);background:#0A1A33;color:#fff;font-family:Cairo,sans-serif;font-size:11px;font-weight:700;padding:4px 10px;border-radius:8px;white-space:nowrap;opacity:0;pointer-events:none;transition:opacity .25s}';
			echo '#rukn-toc-tab:hover::after{opacity:1}';

			# اللوحة — مخفية بـ visibility حتى لو transform التفّ مع overflow-x في RTL
			echo '#rukn-toc-panel{position:fixed;top:0;inset-inline-end:0;inset-inline-start:auto;height:100dvh;width:min(320px,78vw);max-width:320px;z-index:1100;background:#fff;box-shadow:-18px 0 50px rgba(10,26,51,.25);display:flex;flex-direction:column;transform:translate3d(100%,0,0);transition:transform .35s cubic-bezier(.2,.8,.25,1),visibility .35s;font-family:Cairo,Tajawal,sans-serif;direction:rtl;visibility:hidden;pointer-events:none}';
			echo 'html[dir="rtl"] #rukn-toc-panel:not(.open){transform:translate3d(-100%,0,0);box-shadow:18px 0 50px rgba(10,26,51,.25)}';
			echo '#rukn-toc-panel.open,html[dir="rtl"] #rukn-toc-panel.open{transform:translate3d(0,0,0);visibility:visible;pointer-events:auto}';
			echo '#rukn-toc-panel .rt-head{display:flex;align-items:center;gap:10px;padding:16px 18px;background:var(--grad,#0A1F4E);color:#fff;font-weight:800;font-size:15.5px}';
			echo '#rukn-toc-panel .rt-head i{color:var(--aqua,#4FA8FF)}';
			echo '#rukn-toc-panel .rt-close{margin-inline-start:auto;background:rgba(255,255,255,.15);border:none;color:#fff;width:28px;height:28px;border-radius:50%;cursor:pointer;display:grid;place-items:center}';
			echo '#rukn-toc-panel .rt-search{padding:12px 14px;border-bottom:1px solid #eef2f6}';
			echo '#rukn-toc-panel .rt-search input{width:100%;border:1.5px solid #e3e9f2;border-radius:10px;padding:9px 12px;font-family:Tajawal,sans-serif;font-size:14px;outline:none}';
			echo '#rukn-toc-panel .rt-search input:focus{border-color:var(--blue,#2E9DF7)}';
			echo '#rukn-toc-panel .rt-list{flex:1;overflow-y:auto;padding:10px 8px}';
			echo '#rukn-toc-panel .rt-list a{display:block;padding:10px 14px;border-radius:10px;color:#33415c;font-weight:700;font-size:14px;line-height:1.6;text-decoration:none;transition:.2s}';
			echo '#rukn-toc-panel .rt-list a:hover{background:rgba(46,157,247,.08);color:var(--turq,#1FB5A3)}';
			echo '#rukn-toc-panel .rt-list a.rt-h3{padding-inline-start:30px;font-weight:400;font-size:13.5px;color:#5b6b85}';
			echo '#rukn-toc-backdrop{position:fixed;inset:0;background:rgba(10,26,51,.45);z-index:1090;opacity:0;pointer-events:none;visibility:hidden;transition:opacity .3s,visibility .3s}';
			echo '#rukn-toc-backdrop.open{opacity:1;pointer-events:auto;visibility:visible}';

			# الموبايل: ممنوع درج عائم يغطي الهيرو/الهيدر
			echo '@media (max-width:768px){';
				echo '#rukn-toc-tab,#rukn-toc-panel,#rukn-toc-backdrop{display:none!important;visibility:hidden!important;pointer-events:none!important;opacity:0!important;transform:none!important}';
			echo '}';

		echo '</style>';
	}

	# ════════════════════════════════════════════════════════════════
	# الماركب + الجافاسكريبت
	# ════════════════════════════════════════════════════════════════
	public function ux_markup_js(){

		if( ! is_singular() ){
			return;
		}

		echo '<script type="text/javascript">';
			echo '(function(){';
				# لا تُبنَى اللوحة على الموبايل — Easy TOC / الشريط الجانبي يكفيان
				echo 'if(window.matchMedia&&window.matchMedia("(max-width:768px)").matches)return;';
				echo 'if(document.querySelector("#ez-toc-container,.ez-toc,.rank-math-toc,.wp-block-rank-math-toc-block,.legal-toc"))return;';
				echo 'var content=document.querySelector(".-single-post-content")||document.querySelector(".article-body");';
				echo 'if(!content)return;';
				echo 'var heads=content.querySelectorAll("h2,h3");';
				echo 'if(heads.length<2)return;';

				echo 'var tab=document.createElement("button");tab.id="rukn-toc-tab";tab.type="button";tab.setAttribute("aria-label","جدول المحتويات");tab.setAttribute("aria-expanded","false");tab.innerHTML=\'<i class="fas fa-list-ul"></i>\';document.body.appendChild(tab);';
				echo 'var backdrop=document.createElement("div");backdrop.id="rukn-toc-backdrop";document.body.appendChild(backdrop);';
				echo 'var panel=document.createElement("div");panel.id="rukn-toc-panel";panel.setAttribute("role","dialog");panel.setAttribute("aria-label","جدول المحتويات");';
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

				echo 'function open(){panel.classList.add("open");backdrop.classList.add("open");tab.setAttribute("aria-expanded","true")}';
				echo 'function close(){panel.classList.remove("open");backdrop.classList.remove("open");tab.setAttribute("aria-expanded","false")}';
				echo 'tab.addEventListener("click",function(){panel.classList.contains("open")?close():open()});';
				echo 'backdrop.addEventListener("click",close);';
				echo 'panel.querySelector(".rt-close").addEventListener("click",close);';
				echo 'document.addEventListener("keydown",function(e){if(e.key==="Escape")close()});';
			echo '})();';
		echo '</script>';
	}

}
new Rukn_UX_Pack;
