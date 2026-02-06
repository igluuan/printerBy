document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const mainContent = document.getElementById('main-content');
    
    let isOpen = false;

    function toggleSidebar(event) {
        event.preventDefault();
        event.stopPropagation();
        
        isOpen = !isOpen;
        
        if (isOpen) {
            sidebar.style.setProperty('right', '0px', 'important');
            if (mainContent) {
                mainContent.style.setProperty('margin-right', '250px', 'important');
            }
        } else {
            sidebar.style.setProperty('right', '-250px', 'important');
            if (mainContent) {
                mainContent.style.setProperty('margin-right', '0', 'important');
            }
        }
        
        console.log('Sidebar aberta:', isOpen);
    }

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', toggleSidebar);
    }
    
    // Garantir que inicia fechada
    sidebar.style.setProperty('right', '-250px', 'important');
});