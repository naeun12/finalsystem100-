<template>
    <div v-if="visible" class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0, 0, 0, 0.5);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg rounded-4 border-0">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-primary">{{ title }}</h5>
                    <button type="button" class="btn-close" @click="cancel" aria-label="Close"></button>
                </div>

                <div class="modal-body py-4">
                    <p class="text-secondary fs-6">{{ message }}</p>
                </div>

                <div class="d-flex justify-content-between gap-3 px-4 pb-4">
                    <button type="button" class="btn btn-danger rounded-3 flex-fill" @click="cancel"
                        style="font-weight: 600;">
                        Cancel
                    </button>
                    <button type="button" class="btn btn-primary rounded-3 flex-fill" @click="confirm"
                        style="font-weight: 600;">
                        {{ functionName }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "ConfirmModal",
    data() {
        return {
            visible: false,
            title: 'Confirmation',
            message: 'Are you sure?',
            functionName: 'name ',
            resolve: null,
            reject: null,
        };
    },
    methods: {
        show({ title = 'Confirmation', message = 'Are you sure?', functionName = '' } = {}) {
            this.title = title;
            this.message = message;
            this.functionName = functionName;
            this.visible = true;

            return new Promise((resolve, reject) => {
                this.resolve = resolve;
                this.reject = reject;
            });
        },
        confirm() {
            this.visible = false;
            this.resolve?.(true);
        },
        cancel() {
            this.visible = false;
        }
    }
};
</script>

<style scoped>
.modal {
    display: block;
}
</style>
