<aside id="lateral">
    <div class="search-bar">
        <img src="Resources/images/buscar.svg" alt="Buscar" class="search-icon">
        <input type="text" placeholder="Buscar" class="search-input" onclick="toggleUserList()">
    </div>

    <!-- Lista de usuarios que aparecerá cuando se haga clic en el input -->
    <div id="user-list" class="user-list">
        <div class="list-header">
            <h3>Recientes</h3>
            <button class="clear-all">Borrar todo</button>
        </div>
        <ul>
            <li>
                <img src="https://via.placeholder.com/40" alt="Usuario 1" class="user-img">
                Usuario 1
                <button class="remove-user">X</button>
            </li>
            <li>
                <img src="https://via.placeholder.com/40" alt="Usuario 2" class="user-img">
                Usuario 2
                <button class="remove-user" >X</button>
            </li>
            <li>
                <img src="https://via.placeholder.com/40" alt="Usuario 3" class="user-img">
                Usuario 3
                <button class="remove-user" >X</button>
            </li>
            <li>
                <img src="https://via.placeholder.com/40" alt="Usuario 4" class="user-img">
                Usuario 4
                <button class="remove-user">X</button>
            </li>
        </ul>
    </div>

    <script>
        function toggleUserList() {
            var userList = document.getElementById('user-list');
            if (userList.style.display === 'none' || userList.style.display === '') {
                userList.style.display = 'block'; // Mostrar la lista
            } else {
                userList.style.display = 'none'; // Ocultar la lista
            }
        }
    </script>
</aside>