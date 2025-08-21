const axios = require('axios');

window.onload = function() {
    const noti = document.getElementById("chat-noti");
    noti.style.display = "none";
    const message = $('#create_message').val();
    axios.get('/chat_box', {
        message
    })
    .then(function (response) {
        const data = response.data
        if(typeof data === "object") {
            if(data.length > 0) {
                const element = $('#chat-content');
                for(let i = 0; i < data.length; i++) {
                    const currentUser = data[i].from_user_id == data[i].chat_box.user_id
                    const row = `
                    <li class="mb-2 ${ currentUser ? 'd-flex justify-content-end' : '' }">
                        <div class="${ currentUser ? 'bg-primary text-white' : 'bg-light text-dark' } py-1 px-3 rounded-pill d-inline-block">
                                ${data[i].message}
                        </div>
                    </li>` 
                    element.append(row)
                }
                document.querySelector("#chat-content").scrollTo(0,document.querySelector("#chat-content").scrollHeight);
                $(".box-content-loading").css("display", "none");
            }
        }
    })
    .catch(function (error) {
        console.log(error);
    });

    setInterval(function(){
        axios.get('/new_message', {
            message
        })
        .then(function (response) {
            const data = response.data
            if(typeof data === "object") {
                if(data.newMessage.length > 0) {
                    const element = $('#chat-content');
                    for(let i = 0; i < data.newMessage.length; i++) {
                        const currentUser = data.newMessage[i].from_user_id == data.newMessage[i].chat_box.user_id
                        const row = `
                        <li class="mb-2 ${ currentUser ? 'd-flex justify-content-end' : '' }">
                            <div class="${ currentUser ? 'bg-primary text-white' : 'bg-light text-dark' } py-1 px-3 rounded-pill d-inline-block">
                                    ${data.newMessage[i].message}
                            </div>
                        </li>` 
                        element.append(row)
                    }

                    document.querySelector("#chat-content").scrollTo(0,document.querySelector("#chat-content").scrollHeight);
                    const noti = document.getElementById("chat-noti");
                    noti.style.display = "";
                }
            }
        })
        .catch(function (error) {
            console.log(error);
        }); 
    }, 5000);

    document.getElementById('create_message').addEventListener('keyup', (e) => {
        if (e.key === 'Enter' || e.keyCode === 13) {
            const message = $('#create_message').val();
            if(message) {
                axios.post('/post_message', {
                    message
                })
                .then(function (response) {
                    const data = response.data
                    if(data.newMessage.length > 0) {
                        const element = $('#chat-content');
                        for(let i = 0; i < data.newMessage.length; i++) {
                            const currentUser = data.newMessage[i].from_user_id == data.newMessage[i].chat_box.user_id
                            const row = `
                            <li class="mb-2 ${ currentUser ? 'd-flex justify-content-end' : '' }">
                                <div class="${ currentUser ? 'bg-primary text-white' : 'bg-light text-dark' } py-1 px-3 rounded-pill d-inline-block">
                                        ${data.newMessage[i].message}
                                </div>
                            </li>` 
                            element.append(row)
                        }
                    }
    
                    document.querySelector("#chat-content").scrollTo(0,document.querySelector("#chat-content").scrollHeight);
                    noti.style.display = "none";
                })
                .catch(function (error) {
                    console.log(error);
                });
                $('#create_message').val('');
            }
        }
    })

    document.getElementById('send_message').addEventListener('click', () => {
        const message = $('#create_message').val();
        axios.post('/post_message', {
            message
        })
        .then(function (response) {
            const data = response.data
            if(data.newMessage.length > 0) {
                const element = $('#chat-content');
                for(let i = 0; i < data.newMessage.length; i++) {
                    const currentUser = data.newMessage[i].from_user_id == data.newMessage[i].chat_box.user_id
                    const row = `
                    <li class="mb-2 ${ currentUser ? 'd-flex justify-content-end' : '' }">
                        <div class="${ currentUser ? 'bg-primary text-white' : 'bg-light text-dark' } py-1 px-3 rounded-pill d-inline-block">
                                ${data.newMessage[i].message}
                        </div>
                    </li>` 
                    element.append(row)
                }
            }

            document.querySelector("#chat-content").scrollTo(0,document.querySelector("#chat-content").scrollHeight);
            const noti = document.getElementById("chat-noti");
            noti.style.display = "none";
        })
        .catch(function (error) {
            console.log(error);
        });
        $('#create_message').val('');
    });
};  
// window.onload = function () {
//     const noti = document.getElementById("chat-noti");
//     const loadContent = function (data) {
//         if (data.length > 0) {
//             const chatContent = $("#chat-content");

//             for (const item of data) {
//                 const isCurrentUser =
//                     item.from_user_id === item.chat_box.user_id;
//                 chatContent.append(`
//                     <li class="mb-1 ${
//                         isCurrentUser ? "d-flex justify-content-end" : ""
//                     }">
//                         <div class="${
//                             isCurrentUser
//                                 ? "bg-primary text-white"
//                                 : "bg-light text-dark"
//                         } py-1 px-3 rounded-pill d-inline-block">
//                                 ${item.message}
//                         </div>
//                     </li>`);
//             }
//             chatContent.animate(
//                 { scrollTop: chatContent.prop("scrollHeight") },
//                 1000
//             );
//         }
//     };
//     const message = $('#create_message').val();
//     axios.get('/chat_box', {
//         message
//     })
//     .then(function (response) {
//         const data = response.data
//         loadContent(data);
//         $(".box-content-loading").css("display", "none");
        
//     })
//     .catch(function (error) {
//         console.log(error);
//     });

//     setInterval(function () {
//         $.ajax({
//             type: "GET",
//             url: "/new_message",
//             success: function (data) {
//                 loadContent(data);
//                 const noti = document.getElementById("chat-noti");
//                 noti.style.display = "";
//             },
//             error: function (e) {
//                 console.log(e);
//             },
//         });

//         axios.get('/new_message', {
//             message
//         })
//         .then(function (response) {
//             const data = response.data
//             loadContent(data);
//             $(".box-content-loading").css("display", "none");
//         })
//         .catch(function (error) {
//             console.log(error);
//         }); 
//     }, 5000);

//     document.getElementById('send_message').addEventListener('click', () => {
//         const message = $('#create_message').val();
//         axios.post('/post_message', {
//             message
//         })
//         .then(function (response) {
//             const data = response.data
//             loadContent(data);
//             $(".box-content-loading").css("display", "none");

//             // document.querySelector("#chat-content").scrollTo(0,document.querySelector("#chat-content").scrollHeight);
//             const noti = document.getElementById("chat-noti");
//             noti.style.display = "none";
//         })
//         .catch(function (error) {
//             console.log(error);
//         });
//         // $('#create_message').val('');
//     });

//     document.getElementById('create_message').addEventListener('keyup', (e) => {
//         if (e.key === 'Enter' || e.keyCode === 13) {
//             const message = $('#create_message').val();
//             if(message) {
//                 axios.post('/post_message', {
//                     message
//                 })
//                 .then(function (response) {
//                     const data = response.data
//                     loadContent(data);
//                     $(".box-content-loading").css("display", "none");
    
//                     document.querySelector("#chat-content").scrollTo(0,document.querySelector("#chat-content").scrollHeight);
//                     noti.style.display = "none";
//                 })
//                 .catch(function (error) {
//                     console.log(error);
//                 });
//                 $('#create_message').val('');
//             }
//         }
//     })
// };
