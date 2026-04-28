<?php
?>
<style>
    
    :root {
        --bg-color: #f8fafc;
        --text-color: #1e293b;
        --card-bg: white;
        --card-shadow: rgba(0,0,0,0.08);
        --header-bg: white;
        --header-shadow: rgba(0,0,0,0.08);
        --sidebar-bg: linear-gradient(180deg, #0ea5e9 0%, #059669 100%);
        --sidebar-text: white;
        --border-color: #f1f5f9;
        --muted-text: #64748b;
        --stat-title-color: #64748b;
        --toggle-bg: #e2e8f0;
        --toggle-thumb: white;
        --department-active: #dcfce7;
        --department-inactive: #fee2e2;
        --department-active-text: #166534;
        --department-inactive-text: #dc2626;
    }

    .dark-mode {
        --bg-color: #0f172a;
        --text-color: #f1f5f9;
        --card-bg: #1e293b;
        --card-shadow: rgba(0,0,0,0.3);
        --header-bg: #1e293b;
        --header-shadow: rgba(0,0,0,0.3);
        --sidebar-bg: linear-gradient(180deg, #0c4a6e 0%, #065f46 100%);
        --sidebar-text: #f1f5f9;
        --border-color: #334155;
        --muted-text: #94a3b8;
        --stat-title-color: #94a3b8;
        --toggle-bg: #334155;
        --toggle-thumb: #0ea5e9;
        --department-active: #166534;
        --department-inactive: #dc2626;
        --department-active-text: #dcfce7;
        --department-inactive-text: #fee2e2;
    }

    
    body {
        background-color: var(--bg-color);
        color: var(--text-color);
        transition: background-color 0.3s ease, color 0.3s ease;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const body = document.body;
        
        if (localStorage.getItem('theme') === 'dark') {
            body.classList.add('dark-mode');
        }

       
        const toggleButton = document.getElementById('theme-toggle');
        if (toggleButton) {
            toggleButton.addEventListener('click', () => {
                body.classList.toggle('dark-mode');
                
                if (body.classList.contains('dark-mode')) {
                    localStorage.setItem('theme', 'dark');
                } else {
                    localStorage.setItem('theme', 'light');
                }
            });
        }
    });
    
    
    (function() {
        if (localStorage.getItem('theme') === 'dark') {
            document.documentElement.classList.add('dark-mode');
        }
    })();
</script>