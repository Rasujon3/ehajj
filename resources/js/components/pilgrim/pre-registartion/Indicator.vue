<script setup>
const {currentStep} = defineProps(['currentStep']);
</script>

<template>
    <div class="row">
        <div class="col-md-4"></div>
        <div class="stepper col-md-4">
            <div :class="['step', currentStep === 1 ? 'current' : currentStep > 1 ? 'active' : '']">
                <div class="circle">1</div>
            </div>


            <div :class="['line', currentStep > 1 ? 'completed' : '']"></div>
            <div :class="['step', currentStep === 2 ? 'current' : currentStep > 2 ? 'active' : '']">
                <div class="circle">2</div>
            </div>

            <div :class="['line', currentStep > 2 ? 'completed' : '']"></div>
            <div :class="['step', currentStep === 3 ? 'current' : currentStep > 3 ? 'active' : '']">
                <div class="circle">3</div>
            </div>

            <div :class="['line', currentStep > 3 ? 'completed' : '']"></div>
            <div :class="['step', currentStep === 4 ? 'current' : currentStep > 4 ? 'active' : '']">
                <div class="circle">4</div>
            </div>
            <div :class="['line', currentStep > 4 ? 'completed' : '']"></div>
            <div :class="['step', currentStep === 5 ? 'current' : currentStep > 5 ? 'active' : '']">
                <div class="circle">5</div>
            </div>
        </div>
        <div class="col-md-4"></div>
    </div>
</template>

<style scoped>
.stepper {
    display: flex;
    align-items: center;
}

.step {
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
}

.circle {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background-color: #e0e0e0;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 5px;
}

.line {
    height: 2px;
    flex-grow: 1;
    background-color: #e0e0e0;
}

.active .circle {
    background-color: #007bff;
    color: white;
}
.current .circle {
    background-color: #858484;
}

.completed {
    background-color: #007bff !important;
}
</style>
