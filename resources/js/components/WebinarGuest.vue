<template>
    <div v-if="!userName">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Enter Name</div>
                    <div class="card-body">
                        <form @submit.prevent="setName">
                            <div class="form-group">
                                <label for="name">Name</label>
                                <div class="input-group">
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="name"
                                        v-model="form.userName"
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
                </div>
            </div>
        </div>
    </div>
    <div v-else-if="isLoading">
        <div class="alert alert-info">
            Loading... This usually takes about 30s
        </div>
    </div>
    <div
        v-else-if="initialSlide === null && !slide.html"
        class="row justify-content-center"
    >
        <div class="alert alert-info">Waiting for the webinar to begin...</div>
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
        </div>
        <div class="row justify-content-center mb-2">
            <div class="col-md-12">
                <div class="p-2 rounded bg-secondary">{{ slide.script }}</div>
            </div>
        </div>
        <div class="row justify-content-center mb-2">
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
                    <div class="card-footer">
                        <form @submit.prevent="sendMessage">
                            <div class="form-group">
                                <label class="visually-hidden" for="chat"
                                    >Chat</label
                                >
                                <div class="input-group">
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="chat"
                                        v-model="chat.message"
                                    />
                                    <button
                                        type="submit"
                                        class="btn btn-primary input-group-append"
                                        :disabled="chatTimer !== null"
                                    >
                                        Send
                                    </button>
                                </div>
                                <small v-text="userName"></small>
                                <small v-if="chatTimer" class="text-muted">
                                    A message can only be sent every 30 seconds
                                </small>
                            </div>
                        </form>
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
            form: { userName: "" },
            userName: "",
            chat: { message: "" },
            chatTimer: null, // Can only send a message every 30 seconds
        };
    },
    beforeMount() {
        if (sessionStorage.getItem("userName")) {
            this.userName = sessionStorage.getItem("userName");
            this.form.userName = this.userName;
        }
    },
    methods: {
        setName() {
            // Must be > 0 characters and < 15 characters:
            if (
                this.form.userName.length < 1 ||
                this.form.userName.length > 15
            ) {
                alert("Name must be between 1 and 15 characters");
                return;
            }
            sessionStorage.setItem("userName", this.form.userName);
            window.location.reload();
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
