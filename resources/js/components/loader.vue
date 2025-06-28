<template>
    <div v-if="loading" class="spinner-overlay">
        <img :src="logoImage" alt="Loading..." class="spinner-bounce" width="100">
    </div>
</template>


<script>
export default {
    name: 'Loader',
    data() {
        return {
            loading: false,
            logoImage: 'http://127.0.0.1:8000/images/Logo/logo.png',

        };
    }
};
</script>

<style>
.spinner-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.8);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}

.loader {
    width: 48px;
    height: 48px;
    display: block;
    margin: 20px auto;
    box-sizing: border-box;
    position: relative;
}

.loader::after {
    content: '';
    width: 48px;
    height: 48px;
    left: 0;
    bottom: 0;
    position: absolute;
    border-radius: 50% 50% 0;
    border: 15px solid red;
    transform: rotate(45deg) translate(0, 0);
    box-sizing: border-box;
    animation: animMarker 0.4s ease-in-out infinite alternate;
}

.loader::before {
    content: '';
    box-sizing: border-box;
    position: absolute;
    left: 0;
    right: 0;
    margin: auto;
    top: 150%;
    width: 24px;
    height: 4px;
    border-radius: 50%;
    background: rgba(0, 0, 0, 0.2);
    animation: animShadow 0.4s ease-in-out infinite alternate;
}

@keyframes animMarker {
    0% {
        transform: rotate(45deg) translate(5px, 5px);
    }

    100% {
        transform: rotate(45deg) translate(-5px, -5px);
    }
}

@keyframes animShadow {
    0% {
        transform: scale(0.5);
    }

    100% {
        transform: scale(1);
    }
}

.spinner-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(255, 255, 255, 0.8);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
    animation: fadeIn 0.5s ease-in-out;
}

/* Fade animation */
@keyframes fadeIn {
    from {
        opacity: 0;
    }

    to {
        opacity: 1;
    }
}

/* Rotate animation for logo */
@keyframes fadeIn {
    from {
        opacity: 0;
    }

    to {
        opacity: 1;
    }
}

.spinner-bounce {
    animation: bounce 1s infinite;
}

/* Bounce animation */
@keyframes bounce {

    0%,
    100% {
        transform: translateY(0);
    }

    50% {
        transform: translateY(-20px);
    }
}

/* Keyframe for rotating image */
</style>
