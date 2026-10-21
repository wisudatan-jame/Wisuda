(function(){
  var D=window.DF||{i18n:{},channels:[]};
  var T=D.i18n;

  /* online channels: one list (from Customize) feeds the contact cards and the footer icons */
  var I={
    web:'<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a14 14 0 010 18M12 3a14 14 0 000 18"/>',
    fb:'<path d="M15 3h-2.5A3.5 3.5 0 009 6.5V9H6.5v3.5H9V21h3.5v-8.5H15l.5-3.5h-3V7a1 1 0 011-1H15z"/>',
    line:'<path d="M12 4C7 4 3 7.2 3 11.2c0 3.6 3.2 6.6 7.5 7.1l-.4 2.2 3.2-2.1C18 18 21 14.9 21 11.2 21 7.2 17 4 12 4z"/>',
    shop:'<path d="M5 8h14l-1 12H6zM9 8V6a3 3 0 016 0v2"/>',
    ig:'<rect x="3.5" y="3.5" width="17" height="17" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.2" cy="6.8" r=".6"/>',
    tt:'<path d="M14 3v11.5a3.5 3.5 0 11-3.5-3.5M14 3c.5 2.6 2.3 4.3 5 4.5"/>',
    yt:'<rect x="2.5" y="5.5" width="19" height="13" rx="3.5"/><path d="M10 9.5v5l4.5-2.5z"/>'
  };
  function esc(s){return String(s).replace(/[&<>"']/g,function(c){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]})}
  function svg(k){return '<svg viewBox="0 0 24 24" aria-hidden="true">'+(I[k]||'')+'</svg>'}
  var chEl=document.getElementById('channels'),soEl=document.getElementById('social');
  if(chEl) chEl.innerHTML=D.channels.map(function(c){return '<a class="ch" href="'+esc(c[3])+'" target="_blank" rel="noopener"><span class="ico">'+svg(c[0])+'</span><span><b>'+esc(c[1])+'</b><small>'+esc(c[2])+'</small></span></a>'}).join('');
  if(soEl) soEl.innerHTML=D.channels.map(function(c){return '<a href="'+esc(c[3])+'" target="_blank" rel="noopener" aria-label="'+esc(c[1])+'">'+svg(c[0])+'</a>'}).join('');

  /* counters in the hero */
  var still=window.matchMedia&&matchMedia('(prefers-reduced-motion: reduce)').matches;
  if(!still){
    [].forEach.call(document.querySelectorAll('[data-count]'),function(el){
      var target=+el.getAttribute('data-count'),t0=null;
      function step(ts){if(!t0)t0=ts;var p=Math.min((ts-t0)/1100,1),v=Math.round(target*(1-Math.pow(1-p,3)));el.textContent=v.toLocaleString('en-US');if(p<1)requestAnimationFrame(step)}
      requestAnimationFrame(step);
    });
  }

  /* mobile menu */
  var burger=document.getElementById('burger'),mnav=document.getElementById('mnav');
  function menu(open){mnav.hidden=!open;burger.setAttribute('aria-expanded',open?'true':'false');burger.setAttribute('aria-label',open?T.menuClose:T.menuOpen)}
  if(burger&&mnav){
    burger.addEventListener('click',function(){menu(mnav.hidden)});
    mnav.addEventListener('click',function(e){if(e.target.closest('a'))menu(false)});
    window.addEventListener('resize',function(){if(window.innerWidth>=1240)menu(false)});
  }

  var hdr=document.getElementById('hdr');
  function onScroll(){hdr.classList.toggle('stuck',window.scrollY>8)}
  if(hdr){onScroll();window.addEventListener('scroll',onScroll,{passive:true})}

  /* industry tabs on the solutions page; ?sector=factory opens a tab directly */
  var VT=[].slice.call(document.querySelectorAll('.vtab'));
  function pick(b){VT.forEach(function(x){var on=x===b;x.classList.toggle('on',on);x.setAttribute('aria-selected',on?'true':'false');var p=document.getElementById('vp-'+x.dataset.v);if(p)p.hidden=!on})}
  VT.forEach(function(b){b.addEventListener('click',function(){pick(b)})});
  var sector=new URLSearchParams(location.search).get('sector');
  if(sector){var tb=document.getElementById('vt-'+sector);if(tb)pick(tb)}

  /* forms post to admin-ajax (inc/leads.php) */
  function send(data){
    return fetch(D.ajax+'?action=df_nonce',{credentials:'same-origin',cache:'no-store'}).then(function(r){return r.json()}).then(function(n){
      var body=new FormData();
      body.append('action','df_lead');body.append('nonce',n.data.nonce);body.append('lang',D.lang);
      Object.keys(data).forEach(function(k){body.append(k,data[k])});
      return fetch(D.ajax,{method:'POST',body:body,credentials:'same-origin'});
    }).then(function(r){return r.json()});
  }
  function val(id){var el=document.getElementById(id);return el?el.value.trim():''}

  var pf=document.getElementById('partnerForm');
  if(pf){
    var hp=document.createElement('input');hp.type='text';hp.name='website';hp.tabIndex=-1;hp.autocomplete='off';hp.className='hp-field';hp.setAttribute('aria-hidden','true');pf.appendChild(hp);
    var ok=document.getElementById('pfOk');if(ok)ok.textContent=T.sent;
    pf.addEventListener('submit',function(e){
      e.preventDefault();
      var bad=[],err=document.getElementById('pfErr');
      [].forEach.call(pf.querySelectorAll('[required]'),function(el){
        var good=el.type==='checkbox'?el.checked:el.value.trim()!==''&&(el.type!=='email'||/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(el.value));
        el.classList.toggle('bad',!good&&el.type!=='checkbox'); if(!good) bad.push(el);
      });
      var tax=document.getElementById('pf-tax'); if(tax&&tax.value&&!/^\d{13}$/.test(tax.value)){tax.classList.add('bad');bad.push(tax)}
      if(bad.length){err.textContent=bad[0].id==='pf-consent'?T.consent:T.required;err.hidden=false;bad[0].focus();return}
      err.hidden=true;
      var btn=pf.querySelector('[type=submit]');btn.disabled=true;btn.classList.add('fsend');
      var type=pf.querySelector('input[name=ptype]:checked');
      var prods=[].map.call(pf.querySelectorAll('input[name=prod]:checked'),function(x){return x.value}).join(', ');
      send({type:'partner',ptype:type?type.value:'dealer',company:val('pf-company'),tax:val('pf-tax'),name:val('pf-name'),pos:val('pf-pos'),tel:val('pf-tel'),email:val('pf-email'),line:val('pf-line'),region:val('pf-region'),years:val('pf-years'),team:val('pf-team'),prod:prods,msg:val('pf-msg'),consent:document.getElementById('pf-consent').checked?'1':'',website:hp.value})
        .then(function(r){if(!r.success)throw 0;pf.reset();ok.hidden=false})
        .catch(function(){err.textContent=T.error;err.hidden=false})
        .then(function(){btn.disabled=false});
    });
  }

  var irf=document.getElementById('irForm');
  if(irf){
    var irOk=document.getElementById('irOk');if(irOk)irOk.textContent=T.irSent;
    irf.addEventListener('submit',function(e){
      e.preventDefault();
      var em=document.getElementById('irEmail');
      if(!/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(em.value.trim())){em.focus();return}
      send({type:'ir',email:em.value.trim()})
        .then(function(r){if(!r.success)throw 0;irOk.textContent=T.irSent;irOk.hidden=false;irf.reset()})
        .catch(function(){irOk.textContent=T.error;irOk.hidden=false});
    });
  }

  /* floating contact button */
  var fb=document.getElementById('fchatBtn'),fl=document.getElementById('fchatList');
  if(fb&&fl){
    fb.addEventListener('click',function(){var open=fl.hidden;fl.hidden=!open;fb.setAttribute('aria-expanded',open?'true':'false')});
    document.addEventListener('keydown',function(e){if(e.key==='Escape'&&!fl.hidden){fl.hidden=true;fb.setAttribute('aria-expanded','false');fb.focus()}});
  }

  /* cookie banner (turn off in Customize when a PDPA plugin handles consent) */
  var ck=document.getElementById('cookie');
  if(ck){
    var saved=null;
    try{saved=localStorage.getItem('df-cookie')}catch(e){}
    if(!saved) ck.hidden=false;
    ck.addEventListener('click',function(e){var b=e.target.closest('button');if(!b)return;try{localStorage.setItem('df-cookie',b.dataset.c)}catch(err){}ck.hidden=true});
  }
})();
