<template lang="">
    <div class="max-w-9xl mx-auto">
        <div class=" overflow-hidden">
            <div class="body">
                <div class="flex items-center justify-center">
                    <p class="content-heading my-7">
                        Generic Pages
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
                            @input="filterPage"
                        />
                    </div>
                    <div class="ml-40">
                        <button @click="openModal" class="w-12 h-12 shadow  text-white hover:opacity-80 focus:shadow-outline focus:outline-none font-bold rounded-lg">
                            <img src="/images/add.svg" alt="Delete" class="w-full">
                        </button>
                        <Page-modal :show="showModal" @close="closeModal" :fields="fields" @save="submitPage"/>
                    </div>
                </div>
                <div v-if="filteredPageList.length > 0" class="table-container">
                    <table class="table">
                        <thead class="thead items-center">
                            <!-- <th>#</th> -->
                            <th>Titles</th>
                            <th>Types</th>
                            <th>Parent Menu</th>
                            <th>Descriptions</th>
                            <th>Actions</th>
                        </thead>
                        <tbody>
                            <tr v-for="(gen,index) in filteredPageList.slice(0, 5)" :key="index" class="row">
                                <!-- <td style="width: 30px; text-align:center; padding: 15px;">{{index + 1}}</td> -->
                                <td style="width: 200px; text-align:center; padding: 15px;">{{gen.title}}</td>
                                <td style="width: 200px; text-align:center; padding: 15px;">{{gen.type}}</td>
                                <td style="width: 200px; text-align:center; padding: 15px;">{{gen.parent}}</td>
                                <td style="width: 50px; text-align:center; padding: 15px;">{{gen.description}}</td>

                                <td style="width: 200px; text-align:center;">
                                    <button @click="edit(gen.id)" >
                                        <img src="/images/edit.svg" alt="" class="icon">
                                    </button>
                                    <button @click="deleteGen(gen.id)" class="ml-3">
                                        <img src="/images/delete.svg" alt="Delete" class="icon">
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <edit-page-modal :show="showEditPageModal" @close="closeEditPageModal" :fields="fields" @save="saveEditedData" />
                <p v-if="noMatchMessage" class="message">{{ noMatchMessage }}</p>
            </div>
        </div>
    </div>
</template>

<script>
    import axios from 'axios';
    import PageModal from '@/Components/PagesModal/PageModal.vue';
    import EditPageModal from '@/Components/PagesModal/EditPageModal.vue';
    import ClassicEditor from '/js/ckeditor_custom';
    import Swal from "sweetalert2";


    export default {

        components:{
            PageModal,
            EditPageModal,
        },

        data() {
            return{ 
                fields: {
                    category: '',
                    parentMenu: '',
                    type: '',
                    title: '',
                    description: '',
                    content: '',
                },
                generic_list: [],
                searchQuery: "",
                filteredPageList: [],
                parentOptions:{},
                noMatchMessage: "",
                showParent: false ,
                editor: ClassicEditor,
                editorData:  '<p>Content of the editor.</p>',
                editorConfig:{},
                showModal: false,
                showEditPageModal: false,
            }
        },

        created(){
            this.initialData();
        },

        methods:{

            initialData() {
                axios.get('/genericlist').then(response => {
                    this.generic_list = response.data;

                    // Reverse the generic_list to display recently uploaded items at the top
                    this.generic_list.reverse();

                    // Initially, display the complete page list
                    this.filteredPageList = this.generic_list;
                });
            },

            deleteGen(id) {
                
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
                        console.log("Deleting QuickLink with ID:", id);
                        console.log("ID for deletion:", id);
                    axios.post('/delete-page', { id}).then(({ data }) => {
                        if (data) {
                            Swal.fire({
                                    icon: "success",
                                    title: "Content Deleted Successfully!",
                                }).then(() => {
                                    this.showModal = false;
                                });
                                this.initialData();
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: "Oops...",
                                text: "Record Cannot be Deleted",
                            });
                        }
                    }).catch(error => {
                        if (error.response && error.response.status === 400) {
                            // Display the server error message in an alert
                            alert(error.response.data.message);
                        } else {
                            console.error('Error deleting record:', error);
                            Swal.fire({
                                    icon: "error",
                                    title: "Oops...",
                                    text: "Something went wrong!",
                                });
                        }
                    })
                } if (result.dismiss === Swal.DismissReason.cancel){
                        Swal.fire("Cancelled", "Your content is safe :)", "info");
                }
            });
            },

        
            edit(id) {
                axios.get(`/content-data/${id}`)
                    .then(response => {
                        this.fields = response.data.details;
                        this.parentOptions = response.data.parentOption;

                        if (this.fields.content === null) {
                            this.fields.content = response.data.content;
                        }

                        // Open the edit modal
                        this.showEditPageModal = true;
                    })
                    .catch(error => {
                        console.error('Error fetching content data:', error);
                    });
            },

            submitPage(){
                axios.post('submit-page',this.fields).then((response)=>{
                    if(response.data){
                        this.initialData();
                        // alert('success');
                        this.fields = {};
                        Swal.fire({
                            icon: "success",
                            title: "Content " + (this.fields.id ? "updated" : "uploaded") + " successfully!",
                        }).then(() => {
                            this.showModal = false;
                        });
                    }else{
                        Swal.fire({
                            icon: "error",
                            title: "Oops...",
                            text: "Something went wrong!",
                        });
                    }
                });
            },

            fetchParentOptions(){
                var id = this.fields.category;
                this.showParent =(id >=2 && id <=5);
                if(this.showParent){
                    axios.get(`/get-parent/${id}`)
                    .then(response => {
                        this.parentOptions = response.data;
                    })
                    .catch(error => {
                        console.error('Error fetching parent items', error);
                    });
                    
                }
            },

            clearFormFields() {
                this.fields = {};
            },

            openEditPageModal() {
                this.showEditPageModal = true;
            },

            closeEditPageModal() {
                this.initialData();
                this.showEditPageModal = false;
            },

            // Function to filter the FAQ list based on the search query
            filterPage() {
                if (this.searchQuery) {
                    this.filteredPageList = this.generic_list.filter((page) => {
                        const query = this.searchQuery.toLowerCase();
                        return (
                            (page.title && page.title.toLowerCase().includes(query)) ||
                            (page.type && page.type.toLowerCase().includes(query)) ||
                            (page.parentMenu && page.parentMenu.toLowerCase().includes(query)) ||
                            (page.description && page.description.toLowerCase().includes(query))
                        );
                    });

                    if (this.filteredPageList.length === 0) {
                        this.noMatchMessage = "No matches found.";
                    } else {
                        this.noMatchMessage = "";
                    }
                } else {
                    this.filteredPageList = this.generic_list;
                    this.noMatchMessage = "";
                }
            },

            openModal() {
                this.showModal = true;
                this.fields = {
                    category:'',
                    parentMenu: '',
                    type: '',
                    title: '',
                    description: '',
                    content: '',
                }
            },

            closeModal() {
                this.initialData();
                this.showModal = false;
            },
        },

        mounted() {
            this.initialData();
            this.editorData="";
        }
    };   
</script>

<style>
     .container{
        width: 100%;
    }
    
    .textArea{
        border: 2px solid #e2e0e0 ;
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
        background-color: #e3e2e2;
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
