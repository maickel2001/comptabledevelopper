async function updateCartBadge(){
  try{
    const res = await fetch('/api/cart.php');
    const data = await res.json();
    const el = document.getElementById('cart-count');
    if(el) el.textContent = data.count || 0;
  }catch(e){}
}

document.addEventListener('click', async (e) => {
  const btn = e.target.closest('.add-to-cart');
  if(!btn) return;
  e.preventDefault();
  const id = Number(btn.dataset.id);
  btn.disabled = true; btn.textContent = 'Added';
  try {
    await fetch('/api/cart.php', {method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({action:'add', id, qty:1})});
    updateCartBadge();
  } finally {
    setTimeout(()=>{btn.disabled=false; btn.textContent='Add to Cart';}, 1000);
  }
});

document.addEventListener('input', async (e) => {
  const qty = e.target.closest('.qty');
  if(!qty) return;
  const id = Number(qty.dataset.id);
  const value = Math.max(0, Number(qty.value || 0));
  await fetch('/api/cart.php', {method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({action:'set', id, qty:value})});
  location.reload();
});

// Search suggestions
const search = document.getElementById('search-input');
if (search) {
  let box; let currentController;
  search.addEventListener('input', async () => {
    const q = search.value.trim();
    if (q.length < 2) { if (box) box.remove(); return; }
    try{
      if(currentController) currentController.abort();
      currentController = new AbortController();
      const res = await fetch('/api/search.php?q='+encodeURIComponent(q), {signal: currentController.signal});
      const items = await res.json();
      if (box) box.remove();
      box = document.createElement('div'); box.className = 'glass'; box.style.position='absolute'; box.style.marginTop='8px'; box.style.zIndex='20';
      items.forEach(it => {
        const a = document.createElement('a'); a.href = 'product.php?id='+it.id; a.textContent = it.name; a.style.display='block'; a.style.padding='8px 12px'; a.style.textDecoration='none'; a.style.color='inherit';
        box.appendChild(a);
      });
      const form = search.closest('form'); form.style.position = 'relative'; form.appendChild(box);
    }catch(err){}
  });
}

updateCartBadge();
