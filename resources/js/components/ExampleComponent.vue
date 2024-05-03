<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Example Component</div>

                    <div class="card-body">I'm an example component.</div>

                    <button @click="testEvent">Test</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    mounted() {
        console.log("Component mounted.");
        window.Echo.channel("test.event").listen("TestEvent", (e) =>
            console.log({ e })
        );
    },
    unmounted() {
        window.Echo.leave("test.event");
    },
    methods: {
        testEvent() {
            axios.post("/api/test");
        },
    },
};
</script>
