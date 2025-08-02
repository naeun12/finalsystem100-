<template>
    <span class="badge px-3 py-2 rounded-pill text-capitalize" :class="statusClass">
        {{ formattedStatus }}
    </span>
</template>

<script>
export default {
    props: {
        status: {
            type: String,
            required: true,
        },
    },
    computed: {
        statusClass() {
            const map = {
                approved: 'bg-success text-white',
                pending: 'bg-warning text-dark',
                confirmed: 'bg-warning text-dark',
                rejected: 'bg-danger text-white',
                cancelled: 'bg-danger text-white',
                expired: 'bg-secondary text-white',
                paid: 'bg-info text-white',
            };

            return map[this.status] || 'bg-secondary text-white';
        },
        formattedStatus() {
            return this.status.replace(/-/g, ' ');
        },
    },
};
</script>
