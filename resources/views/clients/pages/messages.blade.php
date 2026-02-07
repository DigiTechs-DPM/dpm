@extends('clients.layouts.layout')

@section('title', 'CRM | Messages')


@section('mian-content')


    <section class="dashboard my">
        <div id="loading-screen"
            style="
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9999;
            text-align: center;
            color: white;
            font-size: 20px;
            line-height: 100vh;">
            Please wait...
        </div>
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-12">
                    <div class="custom-chat-body">
                        <div class="chat-header">
                            <div class="chat-profile">
                                <div class="profile-detail">
                                    <button>
                                        <img id="profile_image" src="https://designcrm.net/uploads/brands/writers.png"
                                            class="img-fluid" alt="" style="max-width: 50px;">
                                    </button>
                                </div>
                                <a class="nav-link" href="#" role="button">
                                    Writers Publishing Lab
                                </a>
                            </div>
                            <div class="maximize-chat">







                            </div>
                        </div>
                        <div class="chat-container">
                            <div class="conversions">



                                <div class="messages sender">
                                    <button>
                                        <img src="https://designcrm.net/images/avatar.png" id="profile_image"
                                            class="img-fluid" alt="">
                                    </button>
                                    <div class="text-area">
                                        <p>Hi</p>


                                        <span class="text-end" style="font-size: 10px; color: grey;">
                                            15 Aug 2025 06:31 PM (EST)
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="chat-footer">
                                <form class="form" action="https://designcrm.net/client/messages"
                                    enctype="multipart/form-data" method="post">
                                    <input type="hidden" name="_token" value="fBAhYqvzMBh7591vjL6iFJcbsUyAUJUjcweoWpNC">
                                    <div class="chat-icons">
                                        <span>
                                            <label for="file-upload" class="custom-file-upload">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22"
                                                    fill="#0076c2" class="bi bi-paperclip" viewBox="0 0 16 16">
                                                    <path
                                                        d="M4.5 3a2.5 2.5 0 0 1 5 0v9a1.5 1.5 0 0 1-3 0V5a.5.5 0 0 1 1 0v7a.5.5 0 0 0 1 0V3a1.5 1.5 0 1 0-3 0v9a2.5 2.5 0 0 0 5 0V5a.5.5 0 0 1 1 0v7a3.5 3.5 0 1 1-7 0z" />
                                                </svg>
                                            </label>
                                            <input id="file-upload" type="file" name="h_Item_Attachments_FileInput[]"
                                                multiple />
                                        </span>
                                    </div>
                                    <textarea name="message" placeholder="Type something..." id=""></textarea>
                                    <button class="send">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="#0076c2"
                                            class="bi bi-send" viewBox="0 0 16 16">
                                            <path
                                                d="M15.854.146a.5.5 0 0 1 .11.54l-5.819 14.547a.75.75 0 0 1-1.329.124l-3.178-4.995L.643 7.184a.75.75 0 0 1 .124-1.33L15.314.037a.5.5 0 0 1 .54.11ZM6.636 10.07l2.761 4.338L14.13 2.576zm6.787-8.201L1.591 6.602l4.339 2.76z" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>

                    </div>


                </div>
            </div>
        </div>
    </section>

@endsection
