<template>
    <div class="row justify-content-center mb-2">
        <div class="col-md-12">
            <div class="d-flex justify-content-between">
                <button class="btn btn-primary" @click="nextSlide">Next</button>
                <button class="btn btn-danger" @click="restart">Restart</button>
            </div>
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
import { webinarMixin } from "../mixins/webinarMixin";
export default {
    mixins: [webinarMixin],
    methods: {
        nextSlide() {
            axios
                .post("/api/slides/next")
                .catch((e) => alert(e.data?.response?.error || e.message));
        },
        restart() {
            axios
                .post("/api/restart")
                .catch((e) => alert(e.data?.response?.error || e.message));
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

            axios
                .post("/api/chat", {
                    message: this.chat.message,
                    author: this.userName,
                })
                .catch((e) => alert(e.data?.response?.error || e.message));
            this.chat.message = "";
        },
    },
};
</script>
