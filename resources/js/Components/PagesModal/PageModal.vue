<template>
    <div class="modal" v-if="show">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        Add Generic Pages
                    </h5>
                    <button type="button" class="close" @click="closeModal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form @submit.prevent="submitPage">
                        <div>
                            <div class="mb-4">
                                <div>
                                    <label for="category" value="Category"  class="label">
                                        Category
                                    </label>
                                </div>
                                <div>
                                    <select id="category" v-model="fields.category" @change="fetchParentOptions" class="!important form-container textarea text-black bg-gray-200 appearance-none border-2 border-gray-200 rounded py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-purple-500">
                                        <option value="" disabled selected>Select Category</option>
                                        <option value="1">Main Menu</option>
                                        <option value="2">Sub Menu Lvl 1</option>
                                        <option value="3">Sub Menu Lvl 2</option>
                                        <option value="4">Sub Menu Lvl 3</option>
                                        <option value="5">Sidebar Menu</option>         
                                    </select>
                                </div>
                            </div>

                            <!-- <div v-show="showParent" > -->
                            <div v-if="this.fields.category !== 1" class="mb-4">
                                <div>
                                    <label for="parentMenu" value="parentMenu" >
                                        Parent Menu
                                    </label>
                                </div>
                                <div> 
                                    <select id="parentMenu" v-model="fields.page_id" class="!important form-container textarea text-black bg-gray-200 appearance-none border-2 border-gray-200 rounded py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-purple-500">
                                        <option v-for="parentMenu in parentOptions" :value=parentMenu.id>{{parentMenu.title}}</option>
                                    </select> 
                                </div>
                            </div>
                            <div class="mb-4">
                                <div>
                                    <label for="type" value="Type" >
                                        Types
                                    </label>
                                </div>
                                <div>
                                    <select id="type" v-model="fields.type" class="!important form-container textarea text-black bg-gray-200 appearance-none border-2 border-gray-200 rounded py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-purple-500">
                                        <option value="" disabled selected>Select Type</option>
                                        <option value="1">Link</option>
                                        <option value="2">Article</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <label for="title" value="title" >Title</label>
                            <input type="text" id="title" v-model="fields.title" class="mt-1 block w-full input-field">    
                        </div>
                        <div  v-if="this.fields.type !== '1'">
                            <div class="mt-4">
                                <label for="description" value="description" >Description</label>
                                <textarea name="description" id="description" v-model="fields.description" cols="30" class="mt-1 block w-full resize-none textarea"></textarea>
                            </div>
                            <div class="mt-4 ">
                                <label for="contents" value="contents" >Content</label>
                                <ckeditor :editor="editor" :config="editorConfig" v-model="fields.content" class=""></ckeditor>
                                <!-- <textarea name="" id="" cols="30" rows="10" v-model="fields.content"></textarea> -->
                            </div>
                        </div>
                        <div class="flex items-center justify-center mt-4">            
                            <button type="Submit" class="bg-green text-white rounded px-4 py-2">
                                SUBMIT
                            </button>
                            <button type="button" @click="clearFormFields" class="w-20 shadow text-white bg-red hover:opacity-70 focus:shadow-outline focus:outline-none font-bold py-2 px-4 rounded mx-2">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import ClassicEditor from '/js/ckeditor_custom';
import Swal from 'sweetalert2';


export default {
    props: {
        show: Boolean,
        fields: Object,
    },

    data() {
        return{ 
            fields: {
                category:'',
                parentMenu: '',
                type: '',
                title: '',
                description: '',
                content: '',

            },
            generic_list: {},
            parentOptions:{},
            showParent: false ,
            editor: ClassicEditor,
            editorData:  '<p>Content of the editor.</p>',
            editorConfig:{}
        }
    },
    created(){
        this.initialData();
    },

    methods:{
        closeModal() {
            this.$emit('close'); // Emit the 'close' event to close the modal
        },

        initialData() {
            axios.get('/genericlist').then(response => {
                this.generic_list = response.data;

                // Sort the generic_list in ascending order based on created_at
                this.generic_list.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));

                // Initially, display the complete page list
                this.filteredPageList = this.generic_list;
            });
        },


        deleteGen(id) {
            if(confirm('Are you sure?')){
                axios.post('/delete-page',{id}).then(({data})=>{
                    if(data){
                        alert('success');
                        this.$emit('success');
                        this.initialData();
                    }else{
                        alert('Record Cannot Be Deleted');
                    }
                }) .catch(error => {
                if (error.response && error.response.status === 400) {
                    // Display the server error message in an alert
                    alert(error.response.data.message);
                } else {
                    console.error('Error deleting record:', error);
                    alert('An error occurred while deleting the record.');
                }
            });
            }else{
                alert('Cancelled')
            }
        //    axios.post(`/delete-content/${id}`).then(response=>{
        //     console.log(response.data.message);
        //    }).catch(error=>{
        //     console.error(error.response.data.message);
        //    });
        },
      
        edit(id){
            axios.get(`/content-data/${id}`).then(response=> {
                this.fields = response.data.details;
                this.parentOptions = response.data.parentOption;

                if (this.fields.content === null) {
                    this.fields.content = response.data.content;
                }
              
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
                        title: "Content is uploaded successfully!",
                    }).then(() => {
                        this.$emit('close');
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
            // Reset the form fields without emitting 'close'
            this.fields = {};
        },
        

    },
    
    mounted() {
        this.editorData="";
    }
   
    
};   
</script>
  
<style scoped>
.modal-title {
    color: rgb(1, 72, 1); 
    text-align: center;
    font-size: 20px; 
    display: block; 
    margin: 0 auto; 
}

.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 999;
}

.modal-content {
    background-color: white;
    border-radius: 8px; /* Increase the border radius for rounded corners */
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    max-width: 1500px; /* Adjust the maximum width to widen the modal */
    width: 100%;
}

.modal-dialog {
    background-color: white;
    border-radius: 8px; /* Increase the border radius for rounded corners */
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    width: 100%; /* Increase the maximum width to widen the modal */
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px;
    border-bottom: 1px solid #ccc;
}

.modal-title {
font-size: 20px;
}

.modal-body {
    padding: 20px;
    max-height: 90vh;
    max-width: auto;
    overflow-y: auto;
}

.input-field {
    background-color: #F3F4F6;
    border: 1px solid #D1D5DB;
    color: #111827;
    font-size: 14px;
    border-radius: 2px;
    padding: 10px;
    outline: none;
    width: 100%;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}


.close {
    cursor: pointer;
}

.textarea{
    background-color: #F3F4F6;
    border: 1px solid #D1D5DB;
    color: #111827;
    font-size: 14px;
    border-radius: 2px;
}

.form-container {
    width: 200px; 

}
</style>