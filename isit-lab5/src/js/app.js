// app.js — клиентская логика страницы заказов: события, DOM, запросы к серверу.

// ============================================================
// Находим нужные элементы страницы (по id из index.html)
// ============================================================
const taskForm = document.getElementById('task-form');
const titleInput = document.getElementById('task-title');       // номер заказа
const courierInput = document.getElementById('task-courier');   // курьер
const addressInput = document.getElementById('task-address');   // адрес
const sumInput = document.getElementById('task-sum');           // сумма
const statusFilter = document.getElementById('status-filter');
const searchInput = document.getElementById('search-input');
const refreshButton = document.getElementById('refresh-button');
const infoBox = document.getElementById('info');
const taskTable = document.getElementById('task-table');
const taskBody = document.getElementById('task-body');
const counter = document.getElementById('counter');
const errorTitle = document.getElementById('error-title');

// В данных хранятся английские слова, а пользователю показываем русские подписи
const STATUS_NAMES = { new: 'Новый', in_progress: 'В пути', done: 'Доставлен' };

// ============================================================
// Вспомогательные функции (готовые, менять не нужно)
// ============================================================

function showInfo(text, isError) {
  infoBox.textContent = text;
  infoBox.className = isError ? 'message error-message' : 'message';
  infoBox.hidden = false;
}

function updateCounter() {
  const count = taskBody.children.length;
  counter.textContent = count;
  taskTable.hidden = (count === 0);
}

function addCell(row, text) {
  const cell = document.createElement('td');
  cell.textContent = text;
  row.appendChild(cell);
  return cell;
}

// ============================================================
// ШАГ 3. Работа с DOM — создание строки заказа
// ============================================================
function createTaskRow(order) {
  const row = document.createElement('tr');

  // Запоминаем id заказа в атрибуте строки
  row.setAttribute('data-id', order.id);

  // Ячейки: №, статус, курьер, адрес, сумма
  addCell(row, order.title || ('Заказ № ' + order.id));
  addCell(row, STATUS_NAMES[order.status] || order.status);
  addCell(row, order.courier || '—');
  addCell(row, order.address || '—');
  addCell(row, order.sum ? order.sum + ' ₽' : '—');

  // Ячейка с кнопками действий
  const actions = document.createElement('td');

  const doneButton = document.createElement('button');
  doneButton.textContent = order.status === 'done' ? 'Вернуть' : 'Доставлен';
  doneButton.addEventListener('click', onDoneClick);
  actions.appendChild(doneButton);

  const deleteButton = document.createElement('button');
  deleteButton.textContent = 'Удалить';
  deleteButton.className = 'danger';
  deleteButton.addEventListener('click', onDeleteClick);
  actions.appendChild(deleteButton);

  row.appendChild(actions);

  // Если заказ уже доставлен — выделяем строку классом done
  if (order.status === 'done') {
    row.classList.add('done');
  }

  return row;
}

// Событие click на кнопке «Доставлен / Вернуть»
function onDoneClick(event) {
  const button = event.target;
  const row = button.closest('tr');

  row.classList.toggle('done');
  const isDone = row.classList.contains('done');

  // Ячейка статуса — вторая по счёту (индекс 1, т.к. первая — это номер заказа)
  row.children[1].textContent = isDone ? 'Доставлен' : 'Новый';
  button.textContent = isDone ? 'Вернуть' : 'Доставлен';
  button.setAttribute('title', isDone ? 'Вернуть в работу' : 'Отметить доставленным');
}

// Событие click на кнопке «Удалить»
function onDeleteClick(event) {
  const row = event.target.closest('tr');
  if (!confirm('Удалить этот заказ?')) {
    return;
  }
  row.remove();
  updateCounter();
}

// ============================================================
// ШАГ 2. Обработка событий — добавление заказа через форму
// ============================================================
function onFormSubmit(event) {
  event.preventDefault();

  const title = titleInput.value.trim();
  const courier = courierInput.value.trim();
  const address = addressInput.value.trim();
  const sum = sumInput.value.trim();

  // Проверка: номер заказа не короче 3 символов
  if (title.length < 3) {
    errorTitle.textContent = 'Введите номер заказа (минимум 3 символа)';
    return;
  }
  errorTitle.textContent = '';

  // Создаём объект заказа
  const order = {
    id: title,                     // используем введённый номер как id
    title: 'Заказ № ' + title,
    status: 'new',
    courier: courier || '—',
    address: address || '—',
    sum: Number(sum) || 0
  };

  // Вставляем строку в начало таблицы
  taskBody.insertBefore(createTaskRow(order), taskBody.firstChild);
  updateCounter();

  infoBox.hidden = true;
  taskForm.reset();
}

// ============================================================
// ШАГ 4. Асинхронные запросы (fetch)
// ============================================================
async function loadTasks(status, text) {
  const url = '/api/orders?status=' + status + '&q=' + encodeURIComponent(text);
  const response = await fetch(url);
  if (!response.ok) {
    throw new Error('Ошибка сервера: ' + response.status);
  }
  return await response.json();
}

async function refreshList() {
  showInfo('Загрузка…', false);
  refreshButton.disabled = true;

  try {
    const orders = await loadTasks(statusFilter.value, searchInput.value.trim());

    taskBody.innerHTML = '';
    for (let i = 0; i < orders.length; i++) {
      taskBody.appendChild(createTaskRow(orders[i]));
    }
    updateCounter();

    if (orders.length === 0) {
      showInfo('Заказы не найдены. Измените фильтр или текст поиска.', false);
    } else {
      showInfo('Загружено заказов: ' + orders.length, false);
    }
  } catch (error) {
    taskBody.innerHTML = '';
    updateCounter();

    if (error.message === 'Failed to fetch') {
      showInfo('Нет связи с сервером. Проверьте подключение.', true);
    } else {
      showInfo('Не удалось загрузить список. ' + error.message, true);
    }
  } finally {
    refreshButton.disabled = false;
  }
}

// ============================================================
// Подключаем обработчики событий
// ============================================================
taskForm.addEventListener('submit', onFormSubmit);
refreshButton.addEventListener('click', refreshList);
statusFilter.addEventListener('change', refreshList);
searchInput.addEventListener('input', refreshList);

// ============================================================
// Запуск страницы
// ============================================================
refreshList();