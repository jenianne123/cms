<template>
    <div class="article-container">

        <!-- Uploaded -->
        <div class="max-w-9xl mx-auto sm:px-6 lg:px-8" style="margin-top: 10px;">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="pb-6 body bg-gray">
                    <div class="flex justify-between items-center">
                        <h3 class="content-heading">
                           Article Page
                        </h3>
                        <button @click="openModal" class="w-52 h-12 shadow bg-green text-white hover:opacity-80 focus:shadow-outline focus:outline-none font-bold py-2 px-4 rounded-lg">
                            ADD Article
                        </button>
                        <add-article :show="showModal" @close="closeModal" :fields="fields" @save="saveArticle"/>
                    </div>
                    
                    <div class="table-container">
                        <table class="table">
                            <thead class="thead items-center">
                                <th>#</th>
                                <th>Titles</th>
                                <th>Types</th>
                                <th>Descriptions</th>
                                <th>Images</th>
                                <th>Actions</th>
                            </thead>
                            <tbody>
                                <tr v-for="(artics,index) in article_list" :key="index">
                                    <td>{{index+1}}</td>
                                    <td>{{artics.title}}</td>
                                    <td>{{artics.type}}</td>
                                    <td>{{artics.description}}</td>
                                    <td>
                                        {{artics.image_id}}
                                        <!-- <img v-if="artics.filename" src="/storage/news/2e58443db83a463465fdbb2da5e34efd.jpg" alt="uls"> -->
                                        <!-- <img v-if="artics.filename" :src="`/storage/news/${artics.filename}`" alt="uls"> -->
                                        <img v-if="artics.filename" :src="getImageUrl(artics.filename, String(artics.type))" style="max-width: 200px; max-height: 200px" alt="uls">
                                    </td>
                                    <td style="width: 200px; text-align: center;">
                                        <button @click="edit(artics.id)" >
                                          EDIT  
                                      
                                        </button>
                                       
                                        <button @click="deleteArticle(artics.id)" class="ml-3">
                                           DELETE
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <edit-article :show="showEdit" @success="closeEditModal" :fields="fields" :existingImage="existingImage" />
                </div>
            </div>
        </div>
       
    </div>
</template>

<script>
    import AddArticle from '@/Pages/ArticleComponent/AddArticle.vue';
    import EditArticle from '@/Pages/ArticleComponent/EditArticle.vue';
    import axios from 'axios';

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

            };
        },

        created() {
            this.initialData();
        },

        methods: {
            async initialData() {
            try {
                const response = await axios.get('/articlelist');
                this.article_list = response.data;
            } catch (error) {
                console.error('Error fetching article list:', error);
            }
        },

        deleteArticle(id){
            if(confirm('Are you sure?')){
                axios.post('/delete-article',{id}).then(({data})=>{
                    if(data){
                        alert('success');
                        this.$emit('success');
                        this.initialData();
                    }else{
                        alert('error');
                    }
                })
            }else{
                alert('Cancelled')
            }
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

        },
    };
</script>

<style>
    .container{
        width: 100%;
    }
    .article-container {
        width: 100%; /* Adjust the width as needed */
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
        background-color: #D9D9D9;

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
        border: 2px solid #696767;
        background-color: white;
        width: 100%;
    }

    .table-container {
        max-height: 900px; /* Adjust the desired fixed height */
        overflow-y: auto; /* Add a vertical scrollbar when the content exceeds the fixed height */
        border: 2px solid #696767;
        background-color: white;
        width: 100%;
        position: relative; /* Create a new stacking context */
    }

    .row{
        border-bottom: 2px solid #696767;
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
        border: 2px solid #696767; 
        text-align: center;
    }

    .label{
        font-family: Calibri;
        font-weight: bold;
        font-size: 25px;
        text-align: center;
    }

    .content-heading {
        border-bottom: 2px solid #FFCD00; 
        width: max-content;
        font-family: Calibri;
        font-weight: bold;
        font-size: 50px;
        height: 70px;
        place-items: center;
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

    .icon{
        width:30px;
        height: auto;
    }
</style>