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
        } else {
            this.slide.html = "<h1>Coming Soon...</h1>";
        }

        if (this.initialChatMessages) {
            this.chatMessages = this.initialChatMessages;
        }
    },
    beforeUnmount() {
        window.Echo.leave("webinar.update");
    },
    watch: {
        // html change re-writws the iframe:
        "slide.html": function () {
            let iframe = this.$refs.iframe;
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
