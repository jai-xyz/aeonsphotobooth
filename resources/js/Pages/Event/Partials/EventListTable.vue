<script setup>
import AButton from '@/Components/AButton.vue';

defineProps({
    events: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <table class="border-separate border-spacing-2 border-2 border-slate-500 table-fixed">
        <thead class="bg-slate-500">
            <tr>
                <th>Event</th>
                <th>Date</th>
                <th>Time</th>
                <th>Address</th>
                <th>Backdrop Type</th>
                <th>Backdrop Color</th>
                <th>Suggestion</th>
                <th>Action</th> 
            </tr>
        </thead>

        <tbody>
            <tr v-for="event in events" :key="event.id">
                <td>{{ event.event }}</td>
                <td>{{ event.date }}</td>
                <td>{{ event.time }}</td>
                <td>{{ event.address }}</td>
                <td>{{ event.backdroptype }}</td>
                <td>{{ event.backdropcolor }}</td>
                <td>{{ event.suggestion }}</td>
                <td><AButton>Edit</AButton></td>
            </tr>
        </tbody>
    </table>
</template>