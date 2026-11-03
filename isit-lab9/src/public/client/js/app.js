// app.js — логика страницы: вывод данных, обработка событий, запросы к серверу (через функции из api.js).

// ============================================================
// 1. Находим нужные элементы страницы (по их id из index.html)
// ============================================================
const loginScreen = document.getElementById('login-screen');
const mainScreen = document.getElementById('main-screen');
const loginForm = document.getElementById('login-form');
const taskForm = document.getElementById('task-form');
const taskBody = document.getElementById('task-body');
const taskTable = document.getElementById('task-table');
const messageBox = document.getElementById('message');
const statusFilter = document.getElementById('status-filter');
const searchInput = document.getElementById('search-input');

// Подписи для статусов и ролей (на сервере хранятся английские слова)
const STATUS_NAMES = { new: 'Новый', in_progress: 'В пути', done: 'Доставлен' };
const PRIORITY_NAMES = { low: 'Низкий', medium: 'Средний', high: 'Высокий' };
const ROLE_NAMES = { manager: 'Менеджер', developer: 'Курьер', tester: 'Клиент' };

const ERROR_IDS = { title: 'title', project_id: 'project', due_date: 'date' };

let projects = [];            // курьеры (в коде оставили имя projects, так как сервер так их называет)
let requestCounter = 0;
let searchTimer = null;

// ============================================================
// 2. Вспомогательные функции
// ============================================================

function showScreen(name) {
    loginScreen.hidden = (name !== 'login');
    mainScreen.hidden = (name !== 'main');
}

function escapeHtml(text) {
    return String(text)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

function formatDate(isoDate) {
    if (!isoDate) return '—';
    const parts = isoDate.split('-');
    return parts[2] + '.' + parts[1] + '.' + parts[0];
}

function projectName(id) {
    for (let i = 0; i < projects.length; i++) {
        if (projects[i].id === id) {
            return projects[i].title;
        }
    }
    return '—';
}

function showMessage(text, isError) {
    messageBox.textContent = text;
    messageBox.className = isError ? 'message error-message' : 'message';
    messageBox.hidden = false;
}

function hideMessage() {
    messageBox.hidden = true;
}

function setError(name, text) {
    const el = document.getElementById('error-' + name);
    if (el) el.textContent = text;
}

function clearErrors() {
    const errors = document.querySelectorAll('.error');
    for (let i = 0; i < errors.length; i++) {
        errors[i].textContent = '';
    }
}

function describeError(error, action) {
    if (error.status === undefined) {
        return 'Нет связи с сервером. Проверьте подключение и повторите.';
    }
    if (error.status === 403) {
        return 'Недостаточно прав для этого действия.';
    }
    return action + ': ' + error.message;
}

// TODO 3.3 (Показ ошибок под полями)
function showFieldErrors(fields) {
    let shown = false;
    for (const name in fields) {
        if (ERROR_IDS[name]) {
            setError(ERROR_IDS[name], fields[name]);
            shown = true;
        }
    }
    return shown;
}

// TODO 4.1 (Сессия истекла)
function handleUnauthorized() {
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    showScreen('login');
    setError('login', 'Сессия истекла. Войдите снова.');
}

// ============================================================
// 3. Работа с DOM: вывод списка заказов в таблицу
// ============================================================
function renderTasks(tasks) {
    const user = currentUser();
    let html = '';

    for (let i = 0; i < tasks.length; i++) {
        const task = tasks[i];

        // TODO 3.2 (Исправлено dueDate на due_date)
        const due = formatDate(task.due_date);

        let actions = '';
        if (task.status !== 'done') {
            actions += `<button class="done-button" data-id="${task.id}">Доставлен</button> `;
        } else {
            actions += '<span class="done-mark">✓ доставлен</span> ';
        }

        // TODO 4.2 (Кнопка "Удалить" только для менеджера)
        if (user && user.role === 'manager') {
            actions += `<button class="danger delete-button" data-id="${task.id}">Удалить</button>`;
        }

        html += `
      <tr>
        <td>${task.id}</td>
        <td>${escapeHtml(task.title)}</td>
        <td>${escapeHtml(projectName(task.project_id))}</td>
        <td>${STATUS_NAMES[task.status] || task.status}</td>
        <td>${PRIORITY_NAMES[task.priority] || '—'}</td>
        <td>${due}</td>
        <td>${actions}</td>
      </tr>`;
    }

    taskBody.innerHTML = html;
    taskTable.hidden = (tasks.length === 0);

    const doneButtons = document.querySelectorAll('.done-button');
    for (let i = 0; i < doneButtons.length; i++) {
        doneButtons[i].addEventListener('click', onDoneClick);
    }
    const deleteButtons = document.querySelectorAll('.delete-button');
    for (let i = 0; i < deleteButtons.length; i++) {
        deleteButtons[i].addEventListener('click', onDeleteClick);
    }
}

// ============================================================
// 4. Загрузка данных с сервера
// ============================================================
async function loadProjects() {
    projects = await getProjects();
    const select = document.getElementById('task-project');
    let html = '';
    for (let i = 0; i < projects.length; i++) {
        html += `<option value="${projects[i].id}">${escapeHtml(projects[i].title)}</option>`;
    }
    select.innerHTML = html;
}

async function refreshList() {
    const myRequest = ++requestCounter;
    showMessage('Загрузка…', false);

    try {
        const tasks = await getTasks(statusFilter.value, searchInput.value.trim());
        if (myRequest !== requestCounter) return;

        renderTasks(tasks);
        if (tasks.length === 0) {
            showMessage('Заказы не найдены. Измените фильтры или создайте новый заказ.', false);
        } else {
            hideMessage();
        }
    } catch (error) {
        if (myRequest !== requestCounter) return;
        renderTasks([]);
        showMessage(describeError(error, 'Не удалось загрузить список'), true);
    }
}

// ============================================================
// 5. Обработка событий
// ============================================================
async function onLoginSubmit(event) {
    event.preventDefault();
    clearErrors();

    const email = document.getElementById('login-email').value.trim();
    const password = document.getElementById('login-password').value;

    let hasError = false;
    if (email.indexOf('@') === -1) {
        setError('email', 'Введите корректный email');
        hasError = true;
    }
    if (password.length < 6) {
        setError('password', 'Пароль — не короче 6 символов');
        hasError = true;
    }
    if (hasError) return;

    const button = document.getElementById('login-button');
    button.disabled = true;
    try {
        const result = await login(email, password);
        localStorage.setItem('token', result.token);
        localStorage.setItem('user', JSON.stringify(result.user));
        openMainScreen();
    } catch (error) {
        if (error.status === 401) {
            setError('login', 'Неверный email или пароль');
        } else if (error.status === undefined) {
            setError('login', 'Нет связи с сервером. Попробуйте позже.');
        } else {
            setError('login', error.message);
        }
    } finally {
        button.disabled = false;
    }
}

async function onTaskSubmit(event) {
    event.preventDefault();
    clearErrors();

    const title = document.getElementById('task-title').value.trim();
    const description = document.getElementById('task-description').value.trim();
    const priority = document.getElementById('task-priority').value;
    const dueDate = document.getElementById('task-date').value;
    const projectId = Number(document.getElementById('task-project').value);

    let hasError = false;
    if (title.length < 3) {
        setError('title', 'Номер заказа — не короче 3 символов');
        hasError = true;
    }
    const today = new Date().toISOString().slice(0, 10);
    if (dueDate !== '' && dueDate < today) {
        setError('date', 'Срок не может быть в прошлом');
        hasError = true;
    }
    if (hasError) return;

    const button = document.getElementById('task-button');
    button.disabled = true;
    try {
        await createTask({
            title: title,
            description: description,
            project_id: projectId,
            priority: priority,
            // TODO 3.2 (Исправлено имя поля и пустая дата)
            due_date: dueDate === '' ? null : dueDate,
            status: 'new'
        });
        taskForm.reset();
        await refreshList();
    } catch (error) {
        if (error.status === 422) {
            if (!showFieldErrors(error.fields)) {
                setError('form', error.message);
            }
        } else {
            setError('form', describeError(error, 'Не удалось создать заказ'));
        }
    } finally {
        button.disabled = false;
    }
}

async function onDoneClick(event) {
    const id = event.target.dataset.id;
    event.target.disabled = true;
    try {
        await updateTask(id, { status: 'done' });
        await refreshList();
    } catch (error) {
        showMessage(describeError(error, 'Не удалось изменить заказ'), true);
    }
}

async function onDeleteClick(event) {
    const id = event.target.dataset.id;
    if (!confirm('Удалить заказ №' + id + '?')) return;

    event.target.disabled = true;
    try {
        await deleteTask(id);
        await refreshList();
    } catch (error) {
        event.target.disabled = false;
        showMessage(describeError(error, 'Не удалось удалить'), true);
    }
}

async function onLogoutClick() {
    await logout();
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    taskBody.innerHTML = '';
    projects = [];
    clearErrors();
    document.getElementById('login-password').value = '';
    showScreen('login');
}

function onSearchInput() {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(refreshList, 300);
}

loginForm.addEventListener('submit', onLoginSubmit);
taskForm.addEventListener('submit', onTaskSubmit);
statusFilter.addEventListener('change', refreshList);
searchInput.addEventListener('input', onSearchInput);
document.getElementById('refresh-button').addEventListener('click', refreshList);
document.getElementById('logout-button').addEventListener('click', onLogoutClick);

// ============================================================
// 6. Запуск страницы
// ============================================================
async function openMainScreen() {
    const user = currentUser();
    document.getElementById('user-name').textContent = user.name;
    document.getElementById('user-role').textContent = ROLE_NAMES[user.role] || user.role;
    showScreen('main');

    try {
        await loadProjects();
    } catch (error) {
        showMessage(describeError(error, 'Не удалось загрузить курьеров'), true);
    }
    await refreshList();
}

async function start() {
    if (!getToken()) {
        showScreen('login');
        return;
    }
    try {
        const user = await me();
        localStorage.setItem('user', JSON.stringify(user));
        openMainScreen();
    } catch (error) {
        showScreen('login');
        if (error.status === undefined) {
            setError('login', 'Нет связи с сервером. Попробуйте позже.');
        }
    }
}

start();