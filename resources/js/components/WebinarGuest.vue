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
    <div v-else class="row justify-content-center">
        <div class="col-md-8" style="height: 80vh; max-height: 1000px">
            <iframe
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
                                <small v-if="chatTimer" class="text-muted">
                                    A message can only be sent every 30 seconds
                                </small>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            form: { userName: "" },
            userName: "",
            slide: {
                html: "",
            },
            chat: { message: "" },
            chatMessages: [],
            chatTimer: null, // Can only send a message every 30 seconds
        };
    },
    beforeMount() {
        if (localStorage.getItem("userName")) {
            this.userName = localStorage.getItem("userName");
            this.form.userName = this.userName;
        }
    },
    mounted() {
        console.log("Component mounted.");
        window.Echo.channel("webinar.update")
            .listen("WebinarChat", (e) =>
                // Add to the start of the array
                this.chatMessages.unshift(e)
            )
            .listen("WebinarSlide", (e) => (this.slide.html = e.html));
    },
    unmounted() {
        window.Echo.leave("webinar.update");
    },
    methods: {
        setName() {
            // Must be > 0 characters and < 15 characters:
            if (
                this.form.userName.length < 1 ||
                this.form.userName.length > 15
            ) {
                alert("Name must be between 1 and 15 characters");
            }
            localStorage.setItem("userName", this.form.userName);
            this.userName = this.form.userName;
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

            document
                .querySelector("iframe")
                .contentDocument.write("<h1>Injected from parent frame</h1>");
            axios.post("/api/chat", {
                message: this.chat.message,
                author: this.userName,
            });
            this.chat.message = "";
        },
    },
};
</script>
