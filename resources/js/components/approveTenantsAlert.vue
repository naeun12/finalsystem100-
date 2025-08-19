<template>
    <div v-if="status" class="alert p-3 rounded shadow-sm text-center" :class="alertClass">
        <strong>Status:</strong> {{ formattedStatus }}
        <p class="mb-0">{{ statusMessage }}</p>
    </div>
</template>

<script>
export default {
    name: 'StatusAlert',
    props: {
        status: {
            type: String,
            required: true
        },
        role: {
            type: String,
            default: 'Tenant'
        }
    },
    computed: {
        alertClass() {
            switch (this.status) {
                case 'active':
                    return 'alert-success';
                case 'moved_out':
                    return 'alert-secondary';
                case 'terminated':
                    return 'alert-danger';
                case 'pending_moveout':
                    return 'alert-warning';
                case 'transferring':
                    return 'alert-info';
                case 'suspended':
                    return 'alert-dark';
                default:
                    return 'alert-light';
            }
        },
        formattedStatus() {
            return this.status.replace('_', ' ').toUpperCase();
        },
        statusMessage() {
            if (this.role === 'Tenant') {
                switch (this.status) {
                    case 'active':
                        return 'You are currently residing in the property. All your records are up to date.';
                    case 'moved_out':
                        return 'You have successfully moved out from the unit. Thank you for staying with us.';
                    case 'terminated':
                        return 'Your tenancy has been terminated. Please contact the administration for further clarification.';
                    case 'pending_moveout':
                        return 'Your request to move out is currently being processed.';
                    case 'transferring':
                        return 'You are currently being transferred to another unit.';
                    case 'suspended':
                        return 'Your account has been suspended. Please resolve the issue with the landlord.';
                    default:
                        return 'Your status is currently being reviewed or updated.';
                }
            } else if (this.role === 'Landlord') {
                switch (this.status) {
                    case 'active':
                        return 'The tenant is currently active and occupying the unit.';
                    case 'moved_out':
                        return 'The tenant has moved out and vacated the unit.';
                    case 'terminated':
                        return 'The tenant’s lease has been terminated due to valid reasons.';
                    case 'pending_moveout':
                        return 'The tenant has initiated a move-out request.';
                    case 'transferring':
                        return 'The tenant is in the process of transferring units.';
                    case 'suspended':
                        return 'The tenant’s access is currently suspended pending issue resolution.';
                    default:
                        return 'No available status information for the tenant.';
                }
            } else {
                return 'Status information unavailable for the specified role.';
            }
        }
    }
};
</script>
