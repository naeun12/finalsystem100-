<template>
    <NotificationList ref="toastRef" />

    <div class="container my-5">
        <h3 class="fw-bold text-primary mb-4 d-flex align-items-center">
            <i class="bi bi-star-fill text-warning me-2"></i>
            Reviews & Ratings
        </h3>
        <!-- Rating Summary -->
        <div class="row align-items-center mb-5 p-4 rounded shadow-sm bg-white">
            <!-- Overall Rating -->
            <div class="col-md-3 text-center border-end">
                <h1 class="display-1 fw-bold text-warning mb-0">{{ averageRating }}</h1>
                <p class="text-muted small mt-2">({{ totalReviewers }} Reviews)</p>
                <div class="text-warning fs-3">
                    <i class="bi bi-star-fill" v-for="n in Math.round(averageRating)" :key="n"></i>
                </div>
            </div>

            <!-- Star Distribution -->
            <div class="col-md-9 ps-4">
                <div v-for="rating in starRatings" :key="rating.stars" class="d-flex align-items-center mb-3">
                    <div style="width: 60px;">
                        <strong class="text-secondary">{{ rating.stars }} ★</strong>
                    </div>
                    <div class="progress flex-grow-1 mx-3"
                        style="height: 12px; border-radius: 6px; background-color: #f1f1f1;">
                        <div class="progress-bar bg-warning" role="progressbar"
                            :style="{ width: `${rating.percentage}%` }" :aria-valuenow="rating.percentage"
                            aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div style="width: 30px;" class="text-end fw-semibold text-dark">
                        {{ rating.count }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Reviews List -->
        <h3 class="mb-4 fw-bold text-primary">Latest Reviews</h3>
        <div class="row g-4">
            <div v-for="review in reviews" :key="review.reviewID" class="col-md-6">
                <div class="card shadow-sm h-100 border-0 rounded-4 review-card">
                    <div class="card-body d-flex gap-3 p-4">
                        <img :src="review.tenant?.pictureID || 'https://via.placeholder.com/60'"
                            alt="User Avatar" class="rounded-circle border border-2 border-warning"
                            style="width: 60px; height: 60px; object-fit: cover;" />
                        <div class="flex-grow-1">
                            <h5 class="card-title mb-1 fw-bold">
                                {{ review.tenant?.firstname }} {{ review.tenant?.lastname }}
                            </h5>
                            <div class="mb-2 text-warning fs-6">
                                <i class="bi bi-star-fill" v-for="i in review.rating" :key="i"></i>
                            </div>
                            <p class="card-text text-secondary">{{ review.review }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: "ReviewsComponent",
    data() {
        return {
            dormitory_id: '',
            tenant_id: '',
            starRatings: [],
            reviews: [],
            averageRating: 0,
            totalReviewers: 0,
        };
    },
    methods: {
        async fetchReviews() {
            try {
                const { data } = await axios.get(
                    `/fetch/reviewsandrating/${this.dormitory_id}`
                );
                this.starRatings = data.starRatings || [];
                this.reviews = data.reviews || [];
                this.averageRating = data.averageRating || 0;
                this.totalReviewers = data.totalReviewers || 0;
                console.log(this.reviews);
            } catch (error) {
                console.error("Error fetching reviews:", error);
            }
        }
    },
    mounted() {
        const element = document.getElementById('reviewandrating');
        if (element) {
            this.dormitory_id = element.dataset.dormId;
            this.tenant_id = element.dataset.tenantId;
            this.fetchReviews();
        }
    }
};
</script>

<style scoped>
.review-card {
    transition: all 0.3s ease-in-out;
}

.review-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
}
</style>
