<x-app-layout>

<div class="flex" style="min-height: calc(100vh - 65px)">

    {{-- SIDEBAR --}}
    <aside class="w-56 bg-white border-r border-orange-100 p-6 flex flex-col gap-1 shrink-0">
        <button
            onclick="setFilter('all')"
            id="filter-all"
            class="text-left px-4 py-2 rounded-lg bg-orange-100 text-orange-600 font-medium transition"
        >Dashboard</button>
        <button
            onclick="setFilter('pending')"
            id="filter-pending"
            class="text-left px-4 py-2 rounded-lg text-gray-600 hover:bg-orange-50 transition"
        >Pending</button>
        <button
            onclick="setFilter('in_progress')"
            id="filter-in_progress"
            class="text-left px-4 py-2 rounded-lg text-gray-600 hover:bg-orange-50 transition"
        >In Progress</button>
        <button
            onclick="setFilter('completed')"
            id="filter-completed"
            class="text-left px-4 py-2 rounded-lg text-gray-600 hover:bg-orange-50 transition"
        >Completed</button>
        <button
            onclick="setFilter('deleted')"
            id="filter-deleted"
            class="text-left px-4 py-2 rounded-lg text-gray-600 hover:bg-orange-50 transition"
        >Deleted</button>
    </aside>

    {{-- Main Content --}}
    <main class="flex-1 p-10 bg-gradient-to-br from-orange-50 via-white to-orange-100">

        {{-- Header --}}
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-bold text-gray-800">Your Tasks</h2>
            <button
                onclick="openTaskModal()"
                class="px-5 py-2.5 bg-orange-500 text-white rounded-xl shadow hover:bg-orange-600 transition font-medium"
            >+ Add Task</button>
        </div>

        {{-- Loading --}}
        <div id="loadingState" class="flex items-center justify-center py-24">
            <span class="text-gray-400 text-lg animate-pulse">Loading tasks…</span>
        </div>

        {{-- Empty State --}}
        <div id="emptyState" class="hidden flex-col items-center justify-center py-24 text-center">
            <div class="text-6xl mb-5">📋</div>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">No tasks here yet</h3>
            <p id="emptyMsg" class="text-gray-400 mb-6">Get started by adding your first task.</p>
            <button
                onclick="openTaskModal()"
                class="px-5 py-2.5 bg-orange-500 text-white rounded-xl hover:bg-orange-600 transition"
            >+ Add Task</button>
        </div>

        {{-- Task Grid --}}
        <div id="taskGrid" class="hidden grid md:grid-cols-2 lg:grid-cols-3 gap-6"></div>

        {{-- Pagination --}}
        <div id="paginationBar" class="hidden mt-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <p id="paginationInfo" class="text-sm text-gray-500"></p>
            <div class="flex items-center gap-3">
                <button
                    id="prevPageBtn"
                    onclick="changePage(-1)"
                    class="px-4 py-2 rounded-lg border border-orange-200 text-gray-600 hover:bg-orange-50 transition disabled:opacity-50 disabled:cursor-not-allowed"
                >Previous</button>
                <span id="pageIndicator" class="text-sm font-medium text-gray-600"></span>
                <button
                    id="nextPageBtn"
                    onclick="changePage(1)"
                    class="px-4 py-2 rounded-lg border border-orange-200 text-gray-600 hover:bg-orange-50 transition disabled:opacity-50 disabled:cursor-not-allowed"
                >Next</button>
            </div>
        </div>

    </main>
</div>

{{-- Add Task Modal --}}
<div id="taskModal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl w-full max-w-md p-6 shadow-xl">

        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-gray-800">Add New Task</h2>
            <button onclick="closeTaskModal()" class="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
        </div>

        {{-- Inline error box --}}
        <div id="formError" class="hidden mb-4 p-3 bg-red-50 border border-red-200 text-red-600 rounded-lg text-sm"></div>

        <form id="taskForm" class="space-y-4" novalidate>

            <input
                type="text" name="title" placeholder="Task title *" required
                class="w-full border border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400"
            />

            <textarea
                name="description" placeholder="Description (optional)" rows="3"
                class="w-full border border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400 resize-none"
            ></textarea>

            <div class="grid grid-cols-2 gap-3">
                <select name="priority" class="w-full border border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400">
                    <option value="0">Low</option>
                    <option value="1" selected>Medium</option>
                    <option value="2">High</option>
                </select>

                <select name="status" class="w-full border border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400">
                    <option value="pending" selected>Pending</option>
                    <option value="in_progress">In Progress</option>
                    <option value="completed">Completed</option>
                </select>
            </div>

            <input
                type="date" name="due_date"
                class="w-full border border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400"
            />

            <div class="flex justify-end gap-3 pt-1">
                <button type="button" onclick="closeTaskModal()" class="px-4 py-2 text-gray-500 hover:text-gray-700">Cancel</button>
                <button
                    id="submitBtn" type="submit"
                    class="px-5 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition font-medium"
                >Add Task</button>
            </div>

        </form>
    </div>
</div>

<script>
    // Code wrapped in DOMContentLoaded to ensure it runs after the axios script is loaded
    document.addEventListener('DOMContentLoaded', () => {
        const axios = window.axios;

        // if axios is not available, show an error message
        if (!axios) {
            showView('empty');
            document.getElementById('emptyMsg').textContent =
                'Frontend assets are not ready. Rebuild Vite and refresh the page.';
            document.querySelector('#emptyState button').classList.add('hidden');
            return;
        }

        // Set CSRF token for all axios requests
        axios.defaults.headers.common['X-CSRF-TOKEN'] =
            document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // App variables
        let allTasks = [];
        let activeFilter = 'all';
        let currentPage = 1;
        let pagination = null;

        // Constants for priority and status labels and styles
        const PRIORITY = {
            0: { label: 'Low', cls: 'bg-green-100 text-green-600' },
            1: { label: 'Medium', cls: 'bg-orange-100 text-orange-600' },
            2: { label: 'High', cls: 'bg-red-100 text-red-600' },
        };

        // Statuses with their labels and dot colors
        const STATUS = {
            pending: { label: 'Pending', dot: 'bg-yellow-400' },
            in_progress: { label: 'In Progress', dot: 'bg-blue-400' },
            completed: { label: 'Completed', dot: 'bg-green-500' },
        };

        // Allowed status changes based on the current status
        function allowedStatuses(currentStatus) {
            if (currentStatus === 'pending') {
                return ['pending', 'in_progress'];
            }

            if (currentStatus === 'in_progress') {
                return ['in_progress', 'completed'];
            }

            return ['completed'];
        }

        // Function to show/hide different views (loading, empty, grid) depending on the view parameter
        function showView(view) {
            // Toggles classes based on the condition to show the correct view and hide others
            document.getElementById('loadingState').classList.toggle('hidden', view !== 'loading');
            document.getElementById('emptyState').classList.toggle('hidden', view !== 'empty');
            document.getElementById('emptyState').classList.toggle('flex', view === 'empty');
            document.getElementById('taskGrid').classList.toggle('hidden', view !== 'grid');
            document.getElementById('taskGrid').classList.toggle('grid', view === 'grid');
            document.getElementById('paginationBar').classList.toggle('hidden', view !== 'grid');
        }

        // Function to escape HTML to prevent XSS in task titles and descriptions 
        function esc(str) {
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;');
        }

        // Function to build the HTML for a single task card based on its properties such as status, priority, and whether it's deleted
        function buildCard(task) {
            const isDeleted = Boolean(task.deleted_at);
            const pri = PRIORITY[task.priority] ?? PRIORITY[0];
            const stat = STATUS[task.status] ?? STATUS.pending;
            const due = task.due_date
                ? `<span class="text-xs text-gray-400 mt-2 block">Due: ${task.due_date}</span>`
                : '';
            const statusOptions = allowedStatuses(task.status)
                .map(status => `<option value="${status}" ${task.status === status ? 'selected' : ''}>${STATUS[status].label}</option>`)
                .join('');

            // Show delete button if task in not deleted
            const deleteButton = isDeleted
                ? ''
                : `
                    <button
                        onclick="deleteTask(${task.id}, this)"
                        class="text-gray-400 hover:text-red-500 transition text-lg leading-none"
                        title="Delete task"
                    >🗑</button>
                `;

            const deletedInfo = isDeleted
                ? `<div class="mt-4 text-xs text-red-500 font-medium">Deleted task</div>`
                : '';

            const statusContent = isDeleted
                ? `<div class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full ${stat.dot} inline-block shrink-0"></span><span class="text-sm text-gray-600">${stat.label}</span></div>`
                : `<div class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full ${stat.dot} inline-block shrink-0"></span><select onchange="updateTaskStatus(${task.id}, this)" class="text-sm border border-gray-200 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-orange-400 bg-white">${statusOptions}</select></div>`;

            return `
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-orange-100 hover:shadow-md transition" data-id="${task.id}">
                <div class="flex justify-between items-start mb-3">
                    <span class="px-3 py-1 text-xs font-medium ${pri.cls} rounded-full">${pri.label}</span>
                    ${deleteButton}
                </div>
                <h3 class="text-base font-semibold text-gray-800 leading-snug">${esc(task.title)}</h3>
                <p class="text-gray-500 mt-2 text-sm leading-relaxed">${esc(task.description ?? '')}</p>
                ${due}
                <div class="mt-5">
                    <label class="text-xs text-gray-500 block mb-1">Status</label>
                    ${statusContent}
                </div>
                ${deletedInfo}
            </div>`;
        }

        // Function to render the tasks on the page based on the current filter and pagination
        function renderTasks() {
            if (!allTasks.length) {
                showView('empty');
                document.getElementById('emptyMsg').textContent =
                    activeFilter === 'all'
                        ? 'Get started by adding your first task.'
                        : activeFilter === 'deleted'
                            ? 'No deleted tasks found.'
                            : `No ${STATUS[activeFilter]?.label ?? activeFilter} tasks.`;
                document.querySelector('#emptyState button').classList.toggle('hidden', activeFilter !== 'all');
                return;
            }

            // Build the HTML for all tasks and insert it into the task grid container, then show the grid view and pagination
            document.getElementById('taskGrid').innerHTML = allTasks.map(buildCard).join('');
            updatePaginationUi();
            showView('grid');
        }

        // Function to update the pagination information and enable/disable pagination buttons based on the current page and total pages
        function updatePaginationUi() {
            const info = document.getElementById('paginationInfo');
            const indicator = document.getElementById('pageIndicator');
            const prevBtn = document.getElementById('prevPageBtn');
            const nextBtn = document.getElementById('nextPageBtn');

            // If pagination data is not available, hide pagination info and disable buttons
            if (!pagination) {
                info.textContent = '';
                indicator.textContent = '';
                prevBtn.disabled = true;
                nextBtn.disabled = true;
                return;
            }

            info.textContent = `Showing ${pagination.from} to ${pagination.to} of ${pagination.total} tasks`;
            indicator.textContent = `Page ${pagination.current_page} of ${pagination.last_page}`;
            prevBtn.disabled = pagination.current_page <= 1;
            nextBtn.disabled = pagination.current_page >= pagination.last_page;
        }

        // Function to update the active state of the sidebar filter buttons based on the currently selected filter
        function updateSidebarActive() {
            ['all', 'pending', 'in_progress', 'completed', 'deleted'].forEach(filter => {
                const btn = document.getElementById(`filter-${filter}`);
                if (!btn) {
                    return;
                }

                // Determine if the current button corresponds to the active filter and toggle classes to highlight it
                const active = filter === activeFilter;
                btn.classList.toggle('bg-orange-100', active);
                btn.classList.toggle('text-orange-600', active);
                btn.classList.toggle('font-medium', active);
                btn.classList.toggle('text-gray-600', !active);
            });
        }

        // Function to load tasks from the API based on the current page and filter
        async function loadTasks(page = 1) {
            showView('loading');
            currentPage = page;

            const params = new URLSearchParams({ page });

            if (activeFilter === 'deleted') {
                params.set('deleted', 'only');
            } else if (activeFilter !== 'all') {
                params.set('status', activeFilter);
            }

            try {
                const { data } = await axios.get(`/api/tasks?${params.toString()}`);
                allTasks = data.data;
                pagination = {
                    current_page: data.current_page,
                    last_page: data.last_page,
                    from: data.from ?? 0,
                    to: data.to ?? 0,
                    total: data.total,
                    per_page: data.per_page,
                };
                renderTasks();
            } catch {
                pagination = null;
                showView('empty');
                document.getElementById('emptyMsg').textContent =
                    'Could not load tasks. Please refresh the page.';
                document.querySelector('#emptyState button').classList.add('hidden');
            }
        }

        // Function to set the active filter when a user clicks on a sidebar button, then reload tasks based on the new filter and reset to the first page
        window.setFilter = function (filter) {
            activeFilter = filter;
            currentPage = 1;
            updateSidebarActive();
            loadTasks(1);
        };

        // Function to change the page when the user clicks on the pagination buttons
        window.changePage = function (direction) {
            if (!pagination) {
                return;
            }

            const nextPage = currentPage + direction;

            if (nextPage < 1 || nextPage > pagination.last_page) {
                return;
            }

            loadTasks(nextPage);
        };

        // Function to open the modal for adding a new task and focus on the title input field
        window.openTaskModal = function () {
            const modal = document.getElementById('taskModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            modal.querySelector('input[name="title"]').focus();
        };

        // Function to close the task modal, reset the form, and hide any error messages
        window.closeTaskModal = function () {
            const modal = document.getElementById('taskModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.getElementById('formError').classList.add('hidden');
            document.getElementById('taskForm').reset();
        };

        // Function to delete a task when the user clicks the delete button on a task card, with a confirmation prompt and error handling
        window.deleteTask = async function (id, btn) {
            if (!confirm('Delete this task?')) {
                return;
            }

            btn.disabled = true;

            try {
                await axios.delete(`/api/tasks/${id}`);
                const targetPage = allTasks.length === 1 && currentPage > 1
                    ? currentPage - 1
                    : currentPage;
                loadTasks(targetPage);
            } catch {
                btn.disabled = false;
                alert('Could not delete task. Please try again.');
            }
        };

        // Function to update the status of a task when the user selects a new status from the dropdown on a task card, with validation to ensure only allowed status changes and error handling
        window.updateTaskStatus = async function (id, selectEl) {
            const nextStatus = selectEl.value;
            const originalStatus = allTasks.find(task => task.id === id)?.status;
            const allowed = allowedStatuses(originalStatus);

            selectEl.disabled = true;

            try {
                const { data: updatedTask } = await axios.patch(`/api/tasks/${id}`, {
                    status: nextStatus,
                });

                // If the updated status does not match the active filter (and the active filter is not 'all')
                // we reload the tasks to reflect the change and avoid showing a task with a status that doesn't match the filter
                const shouldReload = activeFilter !== 'all' && updatedTask.status !== activeFilter;

                if (shouldReload) {
                    loadTasks(currentPage);
                    return;
                }

                allTasks = allTasks.map(task => task.id === id ? { ...task, ...updatedTask } : task);
                renderTasks();
            } catch {
                selectEl.value = originalStatus;
                alert('Could not update task status. Please try again.');
            } finally {
                selectEl.disabled = false;
            }
        };

        // Event listener for the task form submission to create a new task, with error handling and UI updates to reflect the new task in the list
        document.getElementById('taskForm').addEventListener('submit', async function (e) {
            e.preventDefault();

            const errBox = document.getElementById('formError');
            const submitBtn = document.getElementById('submitBtn');

            errBox.classList.add('hidden');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Saving…';

            // Gather form data and construct the payload for the API request
            const fd = new FormData(this);
            const payload = {
                title: fd.get('title').trim(),
                description: fd.get('description').trim() || null,
                priority: parseInt(fd.get('priority'), 10),
                status: fd.get('status'),
                due_date: fd.get('due_date') || null,
            };

            try {
                // Make API request to create a new task with the form data, then close the modal and reload tasks to show the new task in the list
                await axios.post('/api/tasks', payload);
                window.closeTaskModal();
                activeFilter = 'all';
                currentPage = 1;
                updateSidebarActive();
                loadTasks(1);
            } catch (err) {
                const data = err.response?.data ?? {};
                const msg = data.errors
                    ? Object.values(data.errors).flat().join(' ')
                    : (data.message || 'Something went wrong.');
                errBox.textContent = msg;
                errBox.classList.remove('hidden');
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Add Task';
            }
        });

        document.getElementById('taskModal').addEventListener('click', function (e) {
            if (e.target === this) {
                window.closeTaskModal();
            }
        });

        updateSidebarActive();
        loadTasks();
    });
</script>

</x-app-layout>

