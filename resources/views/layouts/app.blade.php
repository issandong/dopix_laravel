<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>@yield('title', 'Dopix')</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  {{-- Si tu utilises Tailwind, il est ici --}}
   @vite('resources/css/app.css') 
  <style>
      :root {
      --main-color: #1a70d2;
      --bg: #f3f6fa;
      --nav-bg: #fff;
      --surface: #fff;
      --shadow: 0 4px 32px #0002;
      --radius: 18px;
    }
    body {
      background: var(--bg);
      font-family: 'Inter', Arial, sans-serif;
      margin: 0;
      padding: 0;
      color: #232949;
    }
    header {
      background: var(--nav-bg);
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 24px 40px 16px 40px;
      box-shadow: 0 2px 4px #0001;
      position: relative;
      z-index: 2;
    }
    .logo {
      font-weight: bold;
      font-size: 1.7rem;
      color: var(--main-color);
      letter-spacing: 1px;
    }
    nav {
      display: flex;
      gap: 24px;
    }
    nav a {
      text-decoration: none;
      color: var(--main-color);
      font-weight: 500;
      transition: color 0.2s;
    }
    nav a:hover { color: #164a8c; }
    .profile {
      display: flex;
      align-items: center;
      gap: 12px;
    }
    
    .container {
      max-width: 720px;
      margin: 40px auto;
      background: var(--surface);
      border-radius: var(--radius);
      box-shadow: var(--shadow);
      padding: 32px;
    }
    .welcome {
      font-size: 1.2rem;
      margin-bottom: 8px;
      font-weight: 400;
    }
    .cta-btn {
      display: inline-block;
      background: var(--main-color);
      color: white;
      padding: 14px 38px;
      border-radius: 24px;
      font-size: 1.1rem;
      font-weight: 600;
      border: none;
      margin: 18px 0 28px 0;
      cursor: pointer;
      transition: background 0.2s;
      box-shadow: 0 2px 8px #1a70d24d;
      text-decoration: none;
    }
    .cta-btn:hover { background: #164a8c;}
    h2 {
      font-size: 1.3rem;
      margin: 30px 0 10px 0;
      letter-spacing: 1px;
      color: #232949;
      font-weight: 600;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      background: var(--surface);
      margin-bottom: 14px;
    }
    th, td {
      padding: 10px 8px;
      text-align: left;
    }
    th {
      background: #e8ecf3;
      font-weight: 600;
      font-size: 1rem;
      border-bottom: 2px solid #dbe3f0;
    }
    tr:nth-child(even) { background: #f7fafd;}
    .status {
      font-weight: 600;
      padding: 4px 12px;
      border-radius: 12px;
      font-size: 0.95rem;
      display: inline-block;
    }
    .status.ok { background: #e4fbe6; color: #267a3e;}
    .status.warn { background: #fff6de; color: #b88a24;}
    .status.bad { background: #fee8e8; color: #ad2020;}
    .history-link {
      color: var(--main-color);
      text-decoration: none;
      font-weight: 500;
      float: right;
      margin-top: 4px;
    }
    .history-link:hover { text-decoration: underline;}
    .plan-badge {
      display: inline-block;
      background: #164a8c;
      color: #fff;
      border-radius: 8px;
      padding: 7px 18px;
      font-size: 0.98rem;
      font-weight: 500;
      margin-top: 12px;
      margin-bottom: 0;
    }
    /* Responsive styles */
    /* Tablet */
    @media(max-width: 900px) {
      .container {  padding: 18px; }
      header { padding: 18px 10px 10px 10px;}
    }
    /* Mobile */
    @media (max-width: 600px) {
      header {
        flex-wrap: wrap;
        padding: 12px 8px 8px 8px;
      }
      .logo { font-size: 1.3rem; }
      nav {
        position: absolute;
        top: 60px;
        left: 0;
        width: 100%;
        flex-direction: column;
        gap: 0;
        background: var(--nav-bg);
        border-bottom-left-radius: 12px;
        border-bottom-right-radius: 12px;
        box-shadow: 0 6px 26px #0001;
        display: none;
      }
      nav.open { display: flex; }
      nav a {
        padding: 16px 0 16px 28px;
        border-bottom: 1px solid #f0f1f6;
      }
      .burger {
        display: block;
        margin-left: auto;
        cursor: pointer;
        width: 34px;
        height: 34px;
        background: none;
        border: none;
      }
      .burger span, .burger span::before, .burger span::after {
        display: block;
        position: absolute;
        width: 26px;
        height: 4px;
        background: #1a70d2;
        border-radius: 2px;
        transition: all 0.3s;
      }
      .burger span {
        position: relative;
        margin: 15px 4px;
      }
      .burger span::before {
        content: '';
        position: absolute;
        top: -9px;
        left: 0;
        width: 26px;
        height: 4px;
        background: #1a70d2;
      }
      .burger span::after {
        content: '';
        position: absolute;
        top: 9px;
        left: 0;
        width: 26px;
        height: 4px;
        background: #1a70d2;
      }
      .profile {
        margin-top: 8px;
      }
      
      .container { padding: 10px;}
      th, td { font-size: 0.97rem;}
      .cta-btn { padding: 10px 20px; font-size: 1rem;}
      /* Table to card for mobile */
      table, thead, tbody, th, td, tr {
        display: block;
      }
      thead { display: none; }
      tr {
        background: #f7fafd;
        margin-bottom: 16px;
        box-shadow: 0 2px 6px #0001;
        border-radius: 10px;
        padding: 8px 0;
      }
      td {
        padding: 8px 10px;
        text-align: left;
        position: relative;
        border: none;
      }
      td:before {
        content: attr(data-label);
        font-weight: 600;
        display: block;
        color: #888;
        margin-bottom: 2px;
        font-size: 0.93rem;
      }
      .history-link {
        float: none;
        display: block;
        margin: 0 auto 12px auto;
        text-align: center;
      }
    }

    .form-control {
  border-radius: 10px;
  border: 1.5px solid #e4e8f0;
  background: #f7fafd;
  padding: 12px 14px;
  margin-bottom: 16px;
  font-size: 1rem;
  color: #232949;
  transition: border-color 0.2s, box-shadow 0.2s;
  outline: none;
  box-shadow: none;
}
.form-control:focus {
  border-color: #b3c6e6;
  background: #fff;
  box-shadow: 0 2px 8px #1a70d221;
}
label {
  font-weight: 500;
  margin-bottom: 4px;
  display: inline-block;
}

.cta-btn {
  border-radius: 24px;
  border: none;
  background: #1a70d2;
  color: #fff;
  font-weight: 600;
  padding: 12px 40px;
  font-size: 1.1rem;
  box-shadow: 0 2px 8px #1a70d24d;
  cursor: pointer;
  margin-top: 12px;
  transition: background 0.2s;
}
.cta-btn:hover {
  background: #164a8c;
}

  </style>
  @stack('head')
</head>
<body>
  @include('partials.header')
  <main class=" px-4 sm:px-6 lg:px-8">
    @yield('content')
  </main>
   @include('partials.footer')
  @stack('scripts')
  <script src="//unpkg.com/alpinejs" defer></script>

  <script>
    let lastScrollTop = 0;
    const footer = document.getElementById('mobileFooter');

    window.addEventListener('scroll', () => {
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

        if (scrollTop > lastScrollTop) {
            // L'utilisateur fait défiler vers le bas → cacher le footer
            footer.style.transform = 'translateY(100%)';
        } else {
            // L'utilisateur remonte → montrer le footer
            footer.style.transform = 'translateY(0)';
        }

        lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
    });
</script>

</body>
</html>