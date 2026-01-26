<div class="mt-6" x-data="{ mini: true }">
    <div class="flex items-center justify-between">
        <h3 class="text-lg font-medium text-gray-100">Calendar</h3>
        <div class="flex items-center gap-2">
            <button x-bind:class="mini ? 'bg-gray-700 text-white px-3 py-1 rounded' : 'bg-transparent text-gray-300 px-3 py-1 rounded'" @click="mini = true; window.toggleCalendarView('dashboard-calendar','mini')">Compact</button>
            <button x-bind:class="!mini ? 'bg-gray-700 text-white px-3 py-1 rounded' : 'bg-transparent text-gray-300 px-3 py-1 rounded'" @click="mini = false; window.toggleCalendarView('dashboard-calendar','full')">Full</button>
        </div>
    </div>
    <div class="mt-3">
        <div id="dashboard-calendar" data-feed-url="{{ route('events.json') }}" data-mode="mini" class="w-full"></div>
    </div>
</div>
