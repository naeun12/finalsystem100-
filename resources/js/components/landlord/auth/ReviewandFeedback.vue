<template>
    <div class="container my-5">
        <!-- Rating Summary -->
        <div class="row align-items-center mb-4">
            <!-- Overall Rating -->
            <div class="col-md-3 text-center">
                <h1 class="display-1 fw-bold text-warning">5.0</h1>
                <p class="text-muted">(25 Reviews)</p>
            </div>

            <!-- Star Distribution -->
            <div class="col-md-9">
                <div v-for="rating in starRatings" :key="rating.stars" class="d-flex align-items-center mb-2">
                    <div style="width: 60px;">
                        <strong>{{ rating.stars }} ★</strong>
                    </div>
                    <div class="progress flex-grow-1 mx-3" style="height: 12px; border-radius: 6px;">
                        <div class="progress-bar bg-warning" role="progressbar"
                            :style="{ width: `${rating.percentage}%` }" :aria-valuenow="rating.percentage"
                            aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div style="width: 30px;" class="text-end">
                        {{ rating.count }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Reviews List -->
        <h3 class="mb-4">Reviews</h3>
        <div class="row g-4">
            <div v-for="review in reviews" :key="review.id" class="col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex gap-3">
                        <img :src="review.avatar" alt="User Avatar" class="rounded-circle"
                            style="width: 60px; height: 60px; object-fit: cover;" />
                        <div class="flex-grow-1">
                            <h5 class="card-title mb-1">{{ review.title }}</h5>
                            <div class="mb-2 text-warning">
                                <span v-for="i in review.rating" :key="i" class="fs-5">&#9733;</span>
                            </div>
                            <h6 class="fst-italic text-secondary mb-2">{{ review.reviewTitle }}</h6>
                            <p class="card-text text-secondary">{{ review.reviewBody }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "ReviewsComponent",
    data() {
        return {
            starRatings: [
                { stars: 5, count: 23, percentage: 92 },
                { stars: 4, count: 3, percentage: 12 },
                { stars: 3, count: 2, percentage: 8 },
                { stars: 2, count: 3, percentage: 12 },
                { stars: 1, count: 2, percentage: 8 },
            ],
            reviews: [
                {
                    id: 1,
                    avatar: "https://via.placeholder.com/50",
                    title: "CSS Tutorials",
                    rating: 5,
                    reviewTitle: "Outstanding Review Design",
                    reviewBody:
                        "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.",
                },
                {
                    id: 2,
                    avatar: "https://via.placeholder.com/50",
                    title: "HTML CSS Tutorials",
                    rating: 5,
                    reviewTitle: "Nice Review Design",
                    reviewBody:
                        "Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos.",
                },
            ],
        };
    },
};
</script>

<style>
/* Optional: card hover effect */
.card:hover {
    transform: translateY(-5px);
    transition: transform 0.2s ease-in-out;
}
</style>
