
    <nav id="menu" class="menu">
        <button class="menu-toggle" id="menuBtn">
            ☰
        </button>
        <div class="actionBar">
            <div>
                <h3 class="menuText ">OPEN EVENTOS</h3>
            </div>
        </div>

        <ul class="optionsBar">

            <?php if ($_SESSION['role'] === 'user' || $_SESSION['role'] === 'promoter' || $_SESSION['role'] === 'master' || $_SESSION['role'] === 'admin' || $_SESSION['role'] === 'recepcionista'): ?>
                <li class="menuItem">
                    <button id="eventosBtn" class="menuOption">
                        <i class="fa-solid fa-star"></i><h5 class="menuText ">Eventos</h5>
                    </button>
                </li>
            <?php endif; ?>   

            <?php if ($_SESSION['role'] === 'master' ): ?>
                <li class="menuItem">
                    <button id="adminBtn" class="menuOption">
                        <i class="fa-solid fa-user-shield"></i><h5 class="menuText ">Administradores</h5>
                    </button>
                </li>
            <?php endif; ?>

            <?php if ($_SESSION['role'] === 'master' || $_SESSION['role'] === 'admin'): ?>
                <li class="menuItem">
                    <button id="promotersBtn" class="menuOption">
                        <i class="fa-brands fa-readme"></i><h5 class="menuText ">Promoters</h5>
                    </button>
                </li>
            <?php endif; ?>   
            <?php if ($_SESSION['role'] === 'master' || $_SESSION['role'] === 'admin'): ?> 
                <li class="menuItem">
                    <button id="recepcionistasBtn" class="menuOption">
                        <i class="fa-solid fa-people-arrows"></i><h5 class="menuText ">Recepcionistas</h5>
                    </button>
                </li>
            <?php endif; ?>   
            <?php if ($_SESSION['role'] === 'promoters' || $_SESSION['role'] === 'master' || $_SESSION['role'] === 'admin'): ?>    
                <li class="menuItem">
                    <button id="clientesBtn" class="menuOption">
                        <i class="fa-solid fa-users"></i><h5 class="menuText ">Clientes</h5>
                    </button>
                </li>
            <?php endif; ?>   
            <?php if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'master' || $_SESSION['role'] === 'admin'): ?>  
                <li class="menuItem">
                    <button id="relatoriosBtn" class="menuOption">
                        <i class="fa-regular fa-folder-open"></i><h5 class="menuText ">Relatorios</h5>
                    </button>
                </li>
            <?php endif; ?>
        </ul>

        <div class="menuUser">
            <a href="#">
                <div>
                    <img src="https://i.imgur.com/ZTs0AKF.png" alt="image">
                </div>
                <h5 class="username menuText "><?php echo $_SESSION['nome'] ?></h5>
                <p id="btnSair" class="menuText "><i class="fa-solid fa-chevron-right"></i></p>
            </a>

            <div class="userInfo">
                <div>
                    <p><?php echo $_SESSION['role'] ?></p>
                </div>
            </div>
        </div>

        <div class="themeBar">
            <div>
                <button id="themeChangeBtn"><i class="fa-solid "></i></button>
            </div>
        </div>
    </nav>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
    const adminBtn = document.getElementById('adminBtn');
    const eventosBtn = document.getElementById('eventosBtn');
    const promotersBtn = document.getElementById('promotersBtn');
    const clientesBtn = document.getElementById('clientesBtn');
    const relatoriosBtn = document.getElementById('relatoriosBtn');
    const recepcionistasBtn = document.getElementById('recepcionistasBtn');

    // Adiciona listener apenas se o botão existir no DOM
    if (adminBtn) {
        adminBtn.addEventListener('click', () => {
            window.location.href = '/open_eventos/pages/admin';
        });
    }

    if (eventosBtn) {
        eventosBtn.addEventListener('click', () => {
            window.location.href = '/open_eventos/pages/eventos';
        });
    }

    if (promotersBtn) {
        promotersBtn.addEventListener('click', () => {
            window.location.href = '/open_eventos/pages/promoters';
        });
    }

    if (clientesBtn) {
        clientesBtn.addEventListener('click', () => {
            window.location.href = '/open_eventos/pages/clientes';
        });
    }

    if (relatoriosBtn) {
        relatoriosBtn.addEventListener('click', () => {
            window.location.href = '/open_eventos/pages/relatorios';
        });
    }

    if (recepcionistasBtn) {
        recepcionistasBtn.addEventListener('click', () => {
            window.location.href = '/open_eventos/pages/recepcionistas';
        });
    }
});

    </script>