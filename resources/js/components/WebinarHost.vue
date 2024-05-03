<template>
    <div class="row justify-content-center mb-2">
        <div class="col-md-12">
            <button class="btn btn-primary" @click="nextSlide">Next</button>
        </div>
    </div>
    <div class="row justify-content-center mb-2">
        <div class="col-md-12">
            <pre class="rounded bg-secondary">{{ slide.script }}</pre>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-8" style="height: 80vh; max-height: 1000px">
            <iframe
                ref="iframe"
                class="w-100"
                style="height: 80vh; max-height: 1000px"
            ></iframe>
        </div>
        <div class="col-md-4" style="height: 80vh; max-height: 1000px">
            <div class="card h-100">
                <div class="card-header">Chat</div>
                <div class="card-body overflow-auto d-flex flex-column-reverse">
                    <div v-for="message in chatMessages">
                        {{ message.message }}
                        <div class="flex">
                            <small class="text-muted me-1">{{
                                message.createdAt
                            }}</small>
                            <small class="text-muted me-1">|</small>
                            <small class="text-muted me-1">{{
                                message.author
                            }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            slide: {
                html: "",
                script: "",
            },
            chatMessages: [],
        };
    },
    mounted() {
        window.Echo.channel("webinar.update")
            .listen("WebinarChat", (e) =>
                // Add to the start of the array
                this.chatMessages.unshift(e)
            )
            .listen("WebinarSlide", (e) => {
                this.slide.html = e.html;
                this.slide.script = e.script;
            });
    },
    unmounted() {
        window.Echo.leave("webinar.update");
    },
    watch: {
        // html change re-writws the iframe:
        "slide.html": function () {
            let iframe = document.querySelector("iframe");
            // first clear all content:
            iframe.contentDocument.body.innerHTML = "";
            document
                .querySelector("iframe")
                .contentDocument.write(this.slide.html);
        },
    },
    methods: {
        nextSlide() {
            axios.post("/api/slides/next");
        },
        sendMessage() {
            // Can only send message every 30 seconds:
            if (this.chatTimer) {
                alert("You can only send a message every 30 seconds");
                return;
            }

            this.chatTimer = setTimeout(() => {
                this.chatTimer = null;
            }, 30000);

            axios.post("/api/chat", {
                message: this.chat.message,
                author: this.userName,
            });
            this.chat.message = "";
        },
    },
};
</script>
