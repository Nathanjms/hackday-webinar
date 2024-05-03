export const webinarMixin = {
    props: {
        initialSlide: Object,
        initialChatMessages: Array,
    },
    data() {
        return {
            slide: {
                html: "",
                script: "",
            },
            chatMessages: [],
            mp3Object: null,
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
                if (e.hasMp3) {
                    this.getMp3();
                }
            })
            .listen("WebinarRestart", (e) => {
                alert("Webinar is restarting...");
                this.chatMessages = [];
                this.slide.html = "";
                this.slide.script = "";
            });

        if (this.initialSlide) {
            this.slide.html = this.initialSlide.html;
            this.slide.script = this.initialSlide.script;
            // Play the base64 encoded mp3:
            if (this.initialSlide.mp3) {
                this.mp3Object = new Audio(
                    "data:audio/mpeg;base64," + this.initialSlide.mp3
                );
                this.mp3Object.play();
            }
        }

        if (this.initialChatMessages) {
            this.chatMessages = this.initialChatMessages;
        }
    },
    beforeUnmount() {
        window.Echo.leave("webinar.update");
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
        getMp3() {
            axios
                .get("/api/slides/mp3")
                .then((response) => {
                    this.mp3Object = new Audio(
                        "data:audio/mpeg;base64," + response.data
                    );
                    this.mp3Object.play();
                })
                .catch(() => {
                    alert("Could not get mp3");
                });
        },
        toggleAudio() {
            if (this.mp3Object) {
                if (this.mp3Object.paused) {
                    this.mp3Object.play();
                } else {
                    this.mp3Object.pause();
                }
            }
        },
    },
};
