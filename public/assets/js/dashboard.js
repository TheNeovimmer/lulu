// ── LUMA Dashboard — Sidebar, AJAX, Toasts ──

document.addEventListener('DOMContentLoaded', function () {
  initSidebar()
  initActiveNav()
  initActions()
  initDropdowns()
})

/* ── Sidebar Toggle ── */
function initSidebar() {
  var toggle = document.getElementById('sidebarToggle')
  var close = document.getElementById('sidebarClose')
  var sidebar = document.getElementById('sidebar')
  var overlay = document.getElementById('sidebarOverlay')
  if (!toggle || !sidebar) return

  function toggleSidebar() {
    if (window.innerWidth < 992) {
      sidebar.classList.toggle('show')
      if (overlay) overlay.classList.toggle('show')
      document.body.style.overflow = sidebar.classList.contains('show') ? 'hidden' : ''
    } else {
      sidebar.classList.toggle('collapsed')
      document.querySelector('.main-dashboard')?.classList.toggle('expanded')
    }
  }

  toggle.addEventListener('click', toggleSidebar)
  if (close) close.addEventListener('click', function () { sidebar.classList.remove('show'); if (overlay) overlay.classList.remove('show'); document.body.style.overflow = '' })
  if (overlay) overlay.addEventListener('click', function () { sidebar.classList.remove('show'); overlay.classList.remove('show'); document.body.style.overflow = '' })

  window.addEventListener('resize', function () {
    if (window.innerWidth >= 992) {
      sidebar.classList.remove('show')
      if (overlay) overlay.classList.remove('show')
      document.body.style.overflow = ''
    }
  })
}

/* ── Active Nav Highlight ── */
function initActiveNav() {
  var path = window.location.pathname
  var links = document.querySelectorAll('.sidebar-nav .nav-link')
  var bestMatch = null
  var bestLen = 0

  links.forEach(function (link) {
    var href = link.getAttribute('href')
    if (!href) return
    link.classList.remove('active')
    if (path === href || (path.startsWith(href) && href.length > bestLen && href !== '/')) {
      bestMatch = link
      bestLen = href.length
    }
  })
  if (bestMatch) bestMatch.classList.add('active')
}

/* ── AJAX Actions ── */
function initActions() {
  document.addEventListener('click', async function (e) {
    var btn = e.target.closest('[data-action]')
    if (!btn) return
    e.preventDefault()

    var action = btn.getAttribute('data-action')
    var url = btn.getAttribute('data-url')
    var confirmMsg = btn.getAttribute('data-confirm')

    if (confirmMsg && !confirm(confirmMsg)) return

    btn.disabled = true
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>'

    try {
      var res = await fetch(url, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      })
      var data = await res.json()

      if (data.success) {
        showToast(data.message || 'Action effectuée', 'success')
        if (data.removeRow) {
          var row = btn.closest('tr')
          if (row) row.style.transition = 'opacity 0.3s'
          if (row) row.style.opacity = '0'
          setTimeout(function () { if (row) row.remove() }, 300)
        }
        if (data.reload) {
          setTimeout(function () { window.location.reload() }, 500)
        }
      } else {
        showToast(data.message || 'Erreur lors de l\'action', 'error')
      }
    } catch (err) {
      showToast('Erreur réseau', 'error')
    }

    btn.disabled = false
    btn.innerHTML = btn.getAttribute('data-original-html') || btn.innerHTML
  })

  // Store original HTML for restore
  document.querySelectorAll('[data-action]').forEach(function (btn) {
    btn.setAttribute('data-original-html', btn.innerHTML)
  })
}

/* ── Toast Notifications ── */
function showToast(message, type) {
  type = type || 'success'
  var container = document.querySelector('.toast-container-dashboard')
  if (!container) {
    container = document.createElement('div')
    container.className = 'toast-container-dashboard'
    document.body.appendChild(container)
  }
  var toast = document.createElement('div')
  toast.className = 'toast-dashboard ' + type
  var icon = type === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-circle-fill'
  toast.innerHTML = '<i class="bi ' + icon + '"></i><span>' + message + '</span>'
  container.appendChild(toast)

  setTimeout(function () {
    toast.classList.add('fade')
    setTimeout(function () { toast.remove() }, 300)
  }, 3000)
}

/* ── Bootstrap Dropdown Init ── */
function initDropdowns() {
  if (window.bootstrap && bootstrap.Dropdown) {
    document.querySelectorAll('.dropdown-toggle').forEach(function (el) {
      new bootstrap.Dropdown(el)
    })
  }
}

/* ── Confirm Delete (for non-AJAX form submissions) ── */
function confirmDelete(url, name) {
  if (confirm('Voulez-vous vraiment supprimer ' + (name || 'cet élément') + ' ?')) {
    var form = document.createElement('form')
    form.method = 'POST'
    form.action = url
    form.style.display = 'none'
    document.body.appendChild(form)
    form.submit()
  }
}
