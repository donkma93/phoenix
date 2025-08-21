const axios = require("axios");

const UserItem = Vue.component("user-item", {
    props: {
        user: Object,
        selectedId: Number,
    },

    data() {
        const avatar = this.user?.chat_box.user.profile.avatar
            ? `/${this.user.chat_box.user.profile.avatar}`
            : "/images/default.jpg";

        return {
            avatar,
        };
    },

    computed: {
        isSelected() {
            if (this.user) {
                return this.selectedId === this.user.chat_box_id;
            }

            return false;
        },

        isNew() {
            if (this.user) {
                if (this.user.staff_get === 1) {
                    return false;
                }

                return true;
            }

            return false;
        },
    },

    methods: {
        choseChatBox(id) {
            console.log(id);
            this.$emit("select", id);
        },
    },

    template: `<li :class="['user-item position-relative row no-gutters align-items-center mb-1 p-2 rounded-lg pointer', isSelected ? 'bg-secondary' : '']"
                 v-if="user" @click="choseChatBox(user.chat_box_id)">
            <div class="col-3 pr-1">
                <img :src="avatar" class="img-fluid rounded-circle avatar-sm" :alt="user.chat_box.user.email" />
            </div>
            <div class="col-9">
                <div :class="['font-16', isNew ? 'font-weight-bold' : '']">{{ user.chat_box.user.email }}</div>
                <p :class="['text-truncate mb-0 text-black-50', isNew ? 'font-weight-bold' : '']">{{ user.message }}</p>
            </div>
        </li>`,
});

const UserList = Vue.component("user-list", {
    components: {
        UserItem,
    },

    data() {
        return {
            users: [],
            search: "",
            selectedId: null,
            isLoading: true,
        };
    },

    async created() {
        await this.getChatDetail(true);
    },

    methods: {
        choseChatBox(id) {
            this.$emit("select", id);
            this.selectedId = id;
        },

        async getChatDetail(showLoading = false) {
            if (showLoading) {
                this.isLoading = true;
            }
            try {
                const response = await axios.get("/staff/messenger/get", {
                    params: {
                        email: this.search,
                    },
                });

                this.isLoading = false;
                this.users = response.data;

                if (showLoading && this.users.length) {
                    this.choseChatBox(this.users[0].chat_box.id);
                }
            } catch (error) {
                console.log(error);
                document
                    .getElementsByClassName("new-message")
                    .forEach((el) => el.remove());
            } finally {
                setTimeout(() => {
                    this.getChatDetail();
                }, 5000);
            }
        },

        keyMonitor(e) {
            if (!this.search) {
                return;
            }
            if (e.key === "Enter" || e.keyCode === 13) {
                this.getChatDetail(true);
            }
        },
    },

    template: `
        <div class="chat-user-list shadow">
            <div class="chat-loading" style="top: 55px" v-if="isLoading">
                <div class="loader">Loading...</div>
            </div>
            <div class="d-flex justify-content-between align-items-center px-3 py-2">
                <h2 class="mb-0 text-dark">Chat</h2>
                <span class="font-20 font-weight-bolder pointer d-block d-lg-none" @click="$emit('hideUserList')">&times;</span>
            </div>
            <div class="mb-3 px-3 py-2">
                <input type="text" placeholder="Find message" class="form-control form-control-sm bg-light border-0 rounded-pill" v-model="search" @keyup="keyMonitor($event)">
            </div>
            <ul class="user-list scroll-height list-unstyled mb-0 py-2 px-3">
                <user-item v-for="user in users" :key="user.id" :user="user" :selectedId="selectedId" @select="choseChatBox"/>
            </ul>
        </div>
    `,
});

const ChatInput = Vue.component("chat-input", {
    props: {
        chatBoxId: Number,
        disabled: Boolean,
    },

    data() {
        return {
            message: "",
            id: -1,
        };
    },

    methods: {
        async keyMonitor(e) {
            if (!this.message) {
                return;
            }
            if (e.key === "Enter" || e.keyCode === 13) {
                await this.sendMessage();
            }
        },

        async buttonClick() {
            await this.sendMessage();
        },

        async sendMessage() {
            try {
                const { message, id } = this;
                this.$emit("pushMessage", {
                    id,
                    message,
                });
                this.message = "";
                this.id = this.id - 1;

                await axios.post("/staff/messenger/send", {
                    chat_box_id: this.chatBoxId,
                    message,
                });

                const nodeItem = document.querySelector(
                    `li[dataid='chat${id}'] > div`
                );

                if (nodeItem) {
                    nodeItem.classList.remove("bg-secondary", "text-white");
                    nodeItem.classList.add("bg-primary", "text-white");
                }
            } catch (error) {
                console.log(error);
                document
                    .getElementsByClassName("new-message")
                    .forEach((el) => el.remove());
            }
        },
    },

    template: `<div class="chat-input bg-white px-3 py-2 border-top border-right d-flex align-items-center justify-content-center">
        <div class="flex-grow-1 pr-3">
            <input type="text" :disabled="disabled" class="form-control rounded-pill bg-light border-0" placeholder="Aa" v-model="message" @keyup="keyMonitor($event)"/>
        </div>
        <i class="fa fa-paper-plane text-primary pointer font-20" aria-hidden="true" @click="buttonClick()"></i>
    </div>`,
});

const ChatItem = Vue.component("chat-item", {
    props: {
        data: Object,
    },

    data() {
        let isCurrentUser = true;
        let isNew = false;
        if (this.data.chat_box && this.data.from_user_id) {
            if (this.data.chat_box.user_id === this.data.from_user_id) {
                isCurrentUser = false;
            }
        }

        if (this.data.is_new) {
            isNew = this.data.is_new;
        }

        return {
            isCurrentUser,
            isNew,
        };
    },

    computed: {
        dataId() {
            return `chat${this.data.id}`;
        },
    },

    mounted() {
        this.$nextTick(() => {
            document
                .querySelector("#chat-content")
                .scrollTo(
                    0,
                    document.querySelector("#chat-content").scrollHeight
                );
        });
    },

    template: `<li :dataId="dataId" :class="['mb-1', isCurrentUser ? 'd-flex justify-content-end' : '', isNew ? 'new-message' : '']">
        <div :class="['py-2 px-3 rounded-pill d-inline-block', isCurrentUser ? isNew ? 'bg-secondary text-white' : 'bg-primary text-white' : 'bg-light text-dark']">
            {{ data.message }}
        </div>
    </li>`,
});

const ChatList = Vue.component("chat-list", {
    components: {
        ChatItem,
        ChatInput,
    },

    props: {
        chatBoxId: Number,
    },

    data() {
        return {
            messageList: [],
            messageListRender: [],
            user: {},
            avatar: "/images/default.jpg",
            isLoading: true,
        };
    },

    watch: {
        chatBoxId() {
            (async () => await this.getMessage(true))();
        },
        messageList(val, oldVal) {
            if (!oldVal.length || val.length > oldVal.length) {
                this.messageListRender = [...val];
            } else {
                this.messageListRender = [...oldVal];
            }
        },
    },

    methods: {
        async getMessage(needLoading = false) {
            if (!this.chatBoxId) {
                return;
            }
            if (needLoading) {
                this.isLoading = true;
            }
            try {
                const response = await axios.post("/staff/messenger/detail", {
                    chat_box_id: this.chatBoxId,
                });

                this.isLoading = false;
                this.messageList = response.data.listChat;
                this.user = response.data.chatBox.user;
                this.avatar = this.user.profile.avatar
                    ? `/${this.user.profile.avatar}`
                    : "/images/default.jpg";
                if (needLoading) {
                    this.$refs.content.scrollTop =
                        this.$refs.content.scrollHeight;
                }
            } catch (error) {
                console.log(error);
            } finally {
                setTimeout(() => {
                    this.getMessage();
                }, 5000);
            }
        },

        pushMessage({ id, message }) {
            this.messageList = [
                ...this.messageList,
                {
                    id,
                    message,
                    chat_box: {
                        user_id: id,
                    },
                    from_user_id: id + 1,
                    is_new: true,
                },
            ];
        },
    },

    template: `<div class="chat-messenger-list d-flex flex-column position-relative">
        <div class="chat-loading" v-if="isLoading">
            <div class="loader">Loading...</div>
        </div>
        <div style="min-height: 62px" class="px-3 py-2 border-bottom shadow-sm d-flex justify-content-between align-items-center position-relative">
            <div class="d-flex align-items-center">
                <img
                    :src="avatar"
                    alt="name"
                    class="img-fluid rounded-circle avatar-sm"
                    v-if="chatBoxId"
                />
                <h5 class="pl-2 m-0 font-weight-bold" v-if="chatBoxId">{{ user.email }}</h5>
            </div>
            <button id="btn-close-user-list" class="btn btn-primary btn-sm rounded-circle" @click="$emit('toggleUserList')">
                <i class="fa fa-ellipsis-h" aria-hidden="true"></i>
            </button>
        </div>
        <ul class="scroll-height chat-list p-3 list-unstyled mb-0 flex-grow-1" id="chat-content" ref="content">
            <chat-item v-for="message in messageListRender" :key="message.id" :data="message" />
        </ul>
        <chat-input @pushMessage="pushMessage" :chatBoxId="chatBoxId" :disabled="isLoading" />
    </div>`,
});

Vue.directive("click-outside", {
    bind: function (el, binding, vnode) {
        el.clickOutsideEvent = function (event) {
            if (
                event.target.contains(
                    document.querySelector("#btn-close-user-list")
                ) ||
                event.target.contains(
                    document.querySelector("#btn-close-user-list i")
                )
            ) {
                return;
            }
            if (!(el === event.target || el.contains(event.target))) {
                vnode.context[binding.expression](event);
            }
        };
        document.body.addEventListener("click", el.clickOutsideEvent);
    },
    unbind: function (el) {
        document.body.removeEventListener("click", el.clickOutsideEvent);
    },
});

const app = new Vue({
    el: "#app",
    components: {
        ChatList,
        UserList,
    },
    data() {
        return {
            isShowUserList: false,
            chatBoxId: null,
            message: "",
        };
    },

    mounted() {
        this.handleResize();
        window.addEventListener("resize", this.handleResize);
    },

    beforeDestroy() {
        window.removeEventListener("resize", this.handleResize);
    },

    methods: {
        toggleUserList() {
            this.isShowUserList = !this.isShowUserList;
        },
        hideUserList() {
            this.isShowUserList = false;
        },
        handleResize() {
            const { clientWidth } = document.documentElement;
            this.isShowUserList = clientWidth >= 992;
        },
        choseChatBox(id) {
            this.chatBoxId = id;
        },
        closeUserList() {
            this.isShowUserList = false;
        },
    },

    template: `
        <div :class="['bg-white text-dark messenger-app', isShowUserList ? 'show' : 'hidden']">
            <chat-list @toggleUserList="toggleUserList" :chatBoxId="chatBoxId" />
            <user-list v-click-outside="closeUserList" @hideUserList="hideUserList" @select="choseChatBox"/>
        </div>
    `,
});
