<template>

    <!-- Uploaded -->
    <div class="max-w-9xl mx-auto">
        <div class="overflow-hidden">
            <div class="body">
                <div class="flex justify-center items-center">
                    <p class="content-heading my-7">
                        Article Page
                    </p>
                </div>
                <div class="flex justify-between items-center max-w-full">
                    <div class="flex justify-between items-center max-w-full" style="width: 600px;">
                        <input
                            type="search"
                            class="relative m-0 -mr-0.5 textArea block w-[1px] min-w-0 flex-auto rounded border bg-transparent bg-clip-padding px-3 py-[0.25rem] text-base font-normal leading-[1.6] text-neutral-700 outline-none transition duration-200 ease-in-out focus:z-[3] focus:border-primary focus:text-neutral-700 focus:shadow-[inset_0_0_0_1px_rgb(59,113,202)] focus:outline-none dark:border-neutral-600 dark:text-neutral-200 dark:placeholder:text-neutral-200 dark:focus:border-primary"
                            placeholder="Search"
                            aria-label="Search"
                            aria-describedby="button-addon1"
                            v-model="searchQuery"
                            @input="filterArticle"
                        />
                    </div>
                <div class="ml-40">
                    <button @click="openModal" class="w-12 h-12 shadow  text-white hover:opacity-80 focus:shadow-outline focus:outline-none font-bold rounded-lg">
                        <img src="/images/add.svg" alt="Delete" class="w-full">
                    </button>
                    <add-article :show="showModal" @close="closeModal" :fields="fields" @save="saveArticle"/>
                </div>
            </div>
                
            <div v-if="filteredArticleList.length > 0" class="table-container">
                <table class="table">
                    <thead class="thead items-center">
                        <th>Titles</th>
                        <th>Types</th>
                        <th>Descriptions</th>
                        <th>Images</th>
                        <th>Actions</th>
                    </thead>
                    <tbody>
                        <tr v-for="(artics,index) in filteredArticleList.slice(0, 5)" :key="index" class="row">
                            <td style="width: 300px; text-align:justify; padding: 15px;">{{artics.title}}</td>
                            <td style="width: 100px; text-align:justify; padding: 15px;">{{articleType[artics.type]}}</td>
                            <td style="width: 300px; text-align:justify; padding: 15px;">{{artics.description}}</td>
                            <td style="width: 100px; text-align:center; padding: 15px;">
                                <!-- <img v-if="artics.filename" src="/storage/news/2e58443db83a463465fdbb2da5e34efd.jpg" alt="uls"> -->
                                <!-- <img v-if="artics.filename" :src="`/storage/news/${artics.filename}`" alt="uls"> -->
                                <img v-if="artics.filename" :src="getImageUrl(artics.filename, String(artics.type))" style="max-width: 200px; max-height: 200px" alt="uls">
                            </td>
                            <td style="width: 200px; text-align: center;">
                                <button @click="edit(artics.id)" >
                                    <img
                                        src="/images/edit.svg"
                                        alt="Edit"
                                        class="icon"
                                    />
                                </button>
                                
                                <button @click="deleteArticle(artics.id)" class="ml-3">
                                    <img
                                        src="/images/delete.svg"
                                        alt="Delete"
                                        class="icon"
                                    />
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
                <edit-article :show="showEdit" @success="closeEditModal" :fields="fields" :existingImage="existingImage" />
                <p v-if="noMatchMessage" class="message">{{ noMatchMessage }}</p>
            </div>
        </div>
    </div>
</template>

<script>
    import AddArticle from '@/Pages/ArticleComponent/AddArticle.vue';
    import EditArticle from '@/Pages/ArticleComponent/EditArticle.vue';
    import axios from 'axios';
    import Swal from "sweetalert2";

    export default {
        components:{
            AddArticle,
            EditArticle,
        },

        data() {
            return {
                fields: {},
                article_list: [],
                // image: '',
                showModal: false,
                showEdit: false,
                existingImage: '' ,
                previewImage: null,
                filteredArticleList: [],
                noMatchMessage: "",

                articleType: {
                    1: 'Announcement',
                    2: 'News',
                    3: 'Events',
                }

            };
        },

        created() {
            this.initialData();
        },

        methods: {

            initialData() {
                axios.get(`/articlelist`).then((response) => {
                    this.article_list = response.data;
                    
                    this.article_list.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));

                    // Initially, display the complete User list
                    this.filteredArticleList = this.article_list;
                });
            },

        deleteArticle(id){
            Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Yes, delete it?",
                    cancelButtonText: "No, cancel",
                    reverseButtons: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        console.log("Deleting Article with ID:", id);
                        console.log("ID for deletion:", id);

                        axios.post('/delete-article',{id}).then(({data})=>{
                            if(data){
                                Swal.fire({
                                    icon: "success",
                                    title: "Content Deleted Successfully!",
                                }).then(() => {
                                    this.showModal = false;
                                });
                                this.initialData();
                            }else{
                                Swal.fire({
                                    icon: "error",
                                    title: "Oops...",
                                    text: "Something went wrong!",
                                });
                            }
                        })
                    }else if (result.dismiss === Swal.DismissReason.cancel){
                        Swal.fire("Cancelled", "Your content is safe :)", "info");
                }
            });
        },

        edit(id){
            axios.get(`/article-data/${id}`).then(response=> {
                this.fields = response.data.details;
                
                this.existingImage = response.data.existingImage;
                this.previewImage = this.getImageUrl(this.existingImage); 

             

                console.log('Image ID during edit:', this.existingImage); 

                if (this.fields.contents === null) {
                    this.fields.contents = response.data.contents;
                }

               

                // if(this.$refs.fileInput){
                //     this.$refs.fileInput.value = this.existingImage;
                //     this.$refs.fileInput.dispatchEvent(new Event('change', { bubbles: true }));
                // }
                this.showEdit = true;
                
            });
        },

            closeEditModal() {
                this.showEdit = false
                this.initialData();
            },

            // Function to filter the FAQ list based on the search query

            openModal() {
                this.showModal = true;
                this.fields = {};
                this.resetPreviewImage();
            },
            resetPreviewImage() {
                this.previewImage = null; // Reset the previewImage variable
            },

            closeModal() {
                this.initialData();
                this.showModal = false;
            },
            getImageUrl(filename, type){
                // return `/storage/news/${filename}`;
                return `/storage/${type === '2' ? 'news' : 'articles'}/${filename}`;
            },

            filterArticle() {
                if (this.searchQuery) {
                    // Filter the Article list when a search query is entered
                    this.filteredArticleList = this.article_list.filter((artic) => {
                        const query = this.searchQuery.toLowerCase();
                        const titleMatch = artic.title.toLowerCase().includes(query);
                        
                        // Get the corresponding type string from the articleType object
                        const typeString = this.articleType[artic.type].toLowerCase();
                        const typeMatch = typeString.includes(query);

                        // Return true if any of the fields match the query
                        return titleMatch || typeMatch;
                    });

                    // Check if there are no matches and set the "no match" message
                    if (this.filteredArticleList.length === 0) {
                        this.noMatchMessage = "No matches found.";
                    } else {
                        this.noMatchMessage = ""; // Clear the "no match" message
                    }
                } else {
                    // Display the complete Article list when the query is empty
                    this.filteredArticleList = this.article_list;
                    this.noMatchMessage = ""; // Clear the "no match" message
                }
            },


        },
    };
</script>

<style>
  .container{
    width: 100%;
  }

  h1{
      color: #0C4B05;
      font-size: 50px;
      font-weight: bold;
      font-family: Calibri;
  }


  #content1{
    color: #e5e7eb;
  }

  .table{
    width: 100%;
  }

  .textArea{
    border: 2px solid #696767 ;
    resize: none;
    height: 50px;
  }

  .body{
        width: 100%;
        margin-left: auto;
        margin-right: auto;

        @media screen and (max-width: 640px) {
            /* This applies to small screens (sm:px-6) */
            padding-left: 6px;
            padding-right: 6px;
        }

        @media (min-width: 1024px) {
            padding-left: 2rem;
            padding-right: 2rem;
        }

    }

    .table{
        box-shadow: inset;
        background-color: white;
        width: 100%;
        border-radius: 10%;
    }

    .table-container {
        max-height: max-content; 
        overflow-y: auto; 
        background-color: white;
        width: 100%;
        position: relative;
        height: fit-content;
        margin-top: 10px;
        margin-bottom: 40px;
    }

    .row{
        border-bottom: 2px solid #e2e0e0;
    }

    .buttonForm{
        border: 1px solid #000000;
        width: 90px;
        height: 40px;
        border-radius: 10px;
        margin: 5px;
        align-items: center;
    }

    .buttonTable{
        margin: 5px;
        align-items: center;
        text-align: center;
        width: 35;
    }

    .buttonDelTable{
        margin: 5px;
        align-items: center;
        text-align: center;
        width: 25;
    }

    .thead{
        background-color: #BDBDBD;
        text-align: center;
    }

    .label{
        font-family: Calibri;
        font-weight: bold;
        font-size: 25px;
        text-align: center;
    }

    .content-heading {
        border-bottom: 2px solid #FFCD00; /* Adjust the color and thickness as needed */
        width: max-content;
        font-family: Calibri;
        font-weight: bold;
        font-size: 50px;
        height: 70px;
    }

    .search{
        width: 900px;
    }

    .message{
        text-align: center;
        font-family: Calibri;
        font-weight: bold;
        font-size: 30px;
    }

    .checkbox-container {
      display: flex;
      align-items: center;
    }

    .no-bullet-list {
      list-style-type: none;
    }

    .switch {
      position: relative;
      display: inline-block;
      width: 60px;
      height: 34px;
    }

    .switch input {
      display: none;
    }

    .slider {
      position: absolute;
      cursor: pointer;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: #ccc;
      -webkit-transition: 0.4s;
      transition: 0.4s;
    }

    .slider:before {
      position: absolute;
      content: "";
      height: 26px;
      width: 26px;
      left: 4px;
      bottom: 4px;
      background-color: white;
      -webkit-transition: 0.4s;
      transition: 0.4s;
    }

    input:checked + .slider {
      background-color: #101010;
    }

    input:focus + .slider {
      box-shadow: 0 0 1px #101010;
    }

    input:checked + .slider:before {
      -webkit-transform: translateX(26px);
      -ms-transform: translateX(26px);
      transform: translateX(26px);
    }

    .slider.round {
      border-radius: 34px;
    }

    .slider.round:before {
      border-radius: 50%;
    }

    .bg-red{
      background-color: red;
    }

  #content1 {
    
    margin-top: 80px;
    background-color: #D9D9D9;
  }

  .icon{
    height: 24px;
    width: auto;
  }
</style>