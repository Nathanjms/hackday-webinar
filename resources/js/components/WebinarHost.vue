<template>
    <div v-if="isLoading">
        <div class="alert alert-info">Loading...</div>
    </div>
    <div
        v-else-if="initialSlide === null && !slide.html"
        class="row justify-content-center mb-2"
    >
        <form @submit.prevent="begin">
            <div class="form-group">
                <label for="topic">Topic</label>
                <div class="input-group">
                    <input
                        type="text"
                        class="form-control"
                        id="topic"
                        v-model="form.topic"
                    />
                    <button
                        type="submit"
                        class="btn btn-primary input-group-append"
                    >
                        Send
                    </button>
                </div>
            </div>
        </form>
    </div>
    <template v-else>
        <div class="row justify-content-center mb-2">
            <div class="col-12 my-2">
                <button class="btn btn-primary me-2" @click="toggleAudio">
                    Toggle Audio
                </button>
                <button class="btn btn-outline-light" @click="restartAudio">
                    Restart Audio
                </button>
            </div>
            <div class="col-md-12">
                <div class="d-flex justify-content-between">
                    <button class="btn btn-primary" @click="nextSlide">
                        Next
                    </button>
                    <button class="btn btn-danger" @click="restart">
                        Restart
                    </button>
                </div>
            </div>
        </div>
        <div class="row justify-content-center mb-2">
            <div class="col-md-12">
                <div class="p-2 rounded bg-secondary">{{ slide.script }}</div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-8 mb-1" style="max-height: 1000px">
                <div
                    class="bg-secondary p-2 rounded w-100"
                    style="max-height: 1000px"
                    v-html="slide.html"
                ></div>
            </div>
            <div class="col-md-4" style="height: 80vh; max-height: 1000px">
                <div class="card h-100">
                    <div class="card-header">Chat</div>
                    <div
                        class="card-body overflow-auto d-flex flex-column-reverse"
                    >
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
</template>

<script>
import { webinarMixin } from "../mixins/webinarMixin";
export default {
    mixins: [webinarMixin],
    data() {
        return {
            form: { topic: "" },
        };
    },
    methods: {
        begin() {
            axios
                .post("/api/webinar/begin", {
                    topic: this.form.topic,
                })
                .catch((e) => alert(e.data?.response?.error || e.message))
                .finally(() => {
                    this.form.topic = "";
                });
        },
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
