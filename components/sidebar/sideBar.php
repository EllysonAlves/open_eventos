
    <nav id="menu" class="menu open">
        <div class="actionBar">
            <div>
                <button id="menuBtn">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <h3 class="menuText open2">OPEN EVENTOS</h3>
            </div>
        </div>

        <ul class="optionsBar">
            
                <li class="menuItem">
                    <a href="#" class="menuOption">
                        <i class="fa-solid fa-house"></i><h5 class="menuText open2">Home</h5>
                    </a>
                </li>
               
            <li class="menuBreak">
                <hr>
            </li>
            <?php if ($_SESSION['role'] === 'user' || $_SESSION['role'] === 'promoters' || $_SESSION['role'] === 'master' || $_SESSION['role'] === 'admin'): ?>
                <li class="menuItem">
                    <button id="eventosBtn" class="menuOption">
                        <i class="fa-solid fa-star"></i><h5 class="menuText open2">Eventos</h5>
                    </button>
                </li>
            <?php endif; ?>   

            <?php if ($_SESSION['role'] === 'master' ): ?>
                <li class="menuItem">
                    <button id="adminBtn" class="menuOption">
                        <i class="fa-solid fa-user-shield"></i><h5 class="menuText open2">Administradores</h5>
                    </button>
                </li>
            <?php endif; ?>

            <?php if ($_SESSION['role'] === 'master' || $_SESSION['role'] === 'admin'): ?>
                <li class="menuItem">
                    <button id="promotersBtn" class="menuOption">
                        <i class="fa-brands fa-readme"></i><h5 class="menuText open2">Promoters</h5>
                    </button>
                </li>
            <?php endif; ?>   
            <?php if ($_SESSION['role'] === 'master' || $_SESSION['role'] === 'admin'): ?> 
                <li class="menuItem">
                    <button id="recepcionistasBtn" class="menuOption">
                        <i class="fa-solid fa-people-arrows"></i><h5 class="menuText open2">Recepcionistas</h5>
                    </button>
                </li>
            <?php endif; ?>   
            <?php if ($_SESSION['role'] === 'promoters' || $_SESSION['role'] === 'master' || $_SESSION['role'] === 'admin'): ?>    
                <li class="menuItem">
                    <button id="clientesBtn" class="menuOption">
                        <i class="fa-solid fa-users"></i><h5 class="menuText open2">Clientes</h5>
                    </button>
                </li>
            <?php endif; ?>   
            <?php if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'master' || $_SESSION['role'] === 'admin'): ?>  
                <li class="menuItem">
                    <button id="relatoriosBtn" class="menuOption">
                        <i class="fa-regular fa-folder-open"></i><h5 class="menuText open2">Relatorios</h5>
                    </button>
                </li>
            <?php endif; ?>
        </ul>

        <div class="menuUser">
            <a href="#">
                <div>
                    <img src="https://i.imgur.com/ZTs0AKF.png" alt="image">
                </div>
                <h5 class="username menuText open2"><?php echo $_SESSION['nome'] ?></h5>
                <p id="btnSair" class="menuText open2"><i class="fa-solid fa-chevron-right"></i></p>
            </a>

            <div class="userInfo">
                <div>
                    <h1><i class="fa-solid fa-exclamation-circle"></i></h1>
                    <p><?php echo $_SESSION['nome'] ?></p>
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
        const adminBtn = document.getElementById('adminBtn').addEventListener('click', () =>{ window.location.href = '/pages/admin';})
        const eventosBtn = document.getElementById('eventosBtn').addEventListener('click', () =>{ window.location.href = '/pages/eventos';})
        const promotersBtn = document.getElementById('promotersBtn').addEventListener('click', () =>{ window.location.href = '/pages/promoters';})
        const clientesBtn = document.getElementById('clientesBtn').addEventListener('click', () =>{ window.location.href = '/pages/clientes';})
        const relatoriosBtn = document.getElementById('relatoriosBtn').addEventListener('click', () =>{ window.location.href = '/pages/relatorios';})
        const recepcionistasBtn = document.getElementById('recepcionistasBtn').addEventListener('click', () =>{ window.location.href = '/pages/recepcionistas';})
    </script>