<template>
    <div class="modal" v-if="show">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        Add Article
                    </h5>
                    <button type="button" class="close" @click="closeModal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form @submit.prevent="saveArticle">
                        <div>
                            <div class="">
                                <label for="title" value="title" class="block mb-2 text-sm font-medium text-gray-900" >Title</label>
                            </div>
                            <div>
                                <input type="text" id="title" v-model="fields.title" class="input-field" required>
                            </div>
                        </div>

                        <div class="mt-4">
                            <label for="description" value="description" class="block mb-2 text-sm font-medium text-gray-900">
                                Description
                            </label>
                            <textarea name="description" id="description" v-model="fields.description" class="input-field resizable" required></textarea>
                        </div>

                        <div class="mt-4 ">
                            <label for="type" value="Type" class="block mb-2 text-sm font-medium text-gray-900" >
                                Types
                            </label>
                            <select id="type" v-model="fields.type" required class="input-field-select">
                                <option value=1>Announcement</option>
                                <option value=2>News</option>
                                <option value=3>Articles</option>
                           </select>
                        </div>

                        <div class="mt-4" v-if="fields.type == 2 || fields.type == 3">
                            <label for="upload" value="image" class="block mb-2 text-sm font-medium text-gray-900 ">
                                Uploads Image
                            </label>
                         
                            <input type="file" accept="image/*"  @change="uploadImage" class="block border border-[#D1D5DB] p-2">
                                <div v-if="previewImage" >
                                
                                    <img :src="previewImage" alt="Image Preview" style="max-width: 200px; max-height: 200px;">
                                </div>
                                <!-- <div v-else>
                                    {{imageShow.length}}
                                    <div v-if="imageShow.length" >
                                        <img :src="getImageUrl(imageShow.imageDetails.filename)" style="max-width: 200px; max-height: 200px" alt="uls">
                                    </div>
                                </div> -->
                        </div>

                        <div class="mt-4" >
                            <label for="contents" value="contents" class="block mb-2 text-sm font-medium text-gray-900">
                                Content
                            </label> 
                            <ckeditor :editor="editor" :config="editorConfig" v-model="fields.contents"></ckeditor>
                           </div>

                        <div class="flex items-center justify-center mt-4">            
                            <button type="Submit" class="w-20 text-white shadow bg-green hover:opacity-70 focus:shadow-outline focus:outline-none font-bold py-2 px-4 rounded mx-2">
                                Submit
                            </button>
                            <button @click="clearFormFields" class="w-20 shadow text-white bg-red hover:opacity-70 focus:shadow-outline focus:outline-none font-bold py-2 px-4 rounded mx-2" type="button">
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
    import Swal from "sweetalert2";

    export default {
        props: {
            show: Boolean,
            fields: Object,
        },
    
        data() {
            return {
                fields: {},
                image: '',
                showParent: false ,
                editor: ClassicEditor,
                editorData:  '<p>Content of the editor.</p>',
                editorConfig:{},
                existingImage: '' ,
                previewImage: null,
            };
        },
    
        methods: {
            closeModal() {
                this.resetPreviewImage();
                this.$emit('close'); // Emit the 'close' event to close the modal
            },
            resetPreviewImage() {
                this.previewImage = null; // Reset the previewImage variable
            },
        
            initialData() {
                axios.get(`/articlelist`).then((response) => {
                    this.article_list = response.data;
                });
            },

            saveArticle(){
                if(this.fields.type == 1){
                    this.saveArtDetails();
                }else{
                    const imageFormData = new FormData();
                    imageFormData.append('image', this.imageFile);
                    imageFormData.append('type', this.fields.type);
                    console.log()

                    if(this.fields.imageid){
                        imageFormData.append('id', this.fields.imageid);
                    }
                    axios.post('upload-image', imageFormData ).then((response) => {
                        if (response.data) { 
                            this.fields.imageid = response.data;
                            this.saveArtDetails();
                            this.resetPreviewImage();
                            this.$emit('close');
                        } else {
                            Swal.fire("Error: Image upload failed but successful post");
                        }
                    })
                    .catch((imageError) => {
                        console.error('An error occurred during image upload:', imageError);
                        Swal.fire("Error: Image upload failed but successful post");
                    });
                }
                
            },

            saveArtDetails(){ 
                axios.post('submit-article',this.fields).then((response)=>{
                    if(response.data){
                            this.initialData(); 
                            this.fields = {};
                            Swal.fire({
                                icon: "success",
                                title: "Content is uploaded successfully!",
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

           
            clearFormFields() {
                this.fields = {};
                thi.closeModal();
            },

            uploadImage(event) {
            const file = event.target.files[0];

            if (file) {
                // Check if the selected file is an image
                const allowedTypes = ['image/jpeg', 'image/png', 'image/gif']; // Add more types if needed
                if (allowedTypes.includes(file.type)) {
                    this.imageFile = file;

                    const reader = new FileReader();
                    reader.onload = () => {
                        this.previewImage = reader.result;
                    };

                    reader.readAsDataURL(file);
                } else {
                    // Alert the user that only image files are allowed
                    Swal.fire('Please select a valid image file (JPEG, PNG, GIF).');
                    // Optionally, you can clear the input value to allow selecting a new file
                    event.target.value = '';
                }
            }
        },
        displayEditorResult(){
            document.getElementById('resultingText').innerHTML = this.editorData;
        },
        emptyEditor(){
            this.editorData='';
        },
        edit(id){
            axios.get(`/article-data/${id}`).then(response=> {
                this.fields = response.data.details;
                
                this.existingImage = response.data.existingImage;
                this.previewImage = this.getImageUrl(this.existingImage); 
             

                console.log('Image ID during edit:', this.fields.imageid); 

                if (this.fields.contents === null) {
                    this.fields.contents = response.data.contents;
                }

                // if(this.$refs.fileInput){
                //     this.$refs.fileInput.value = this.existingImage;
                //     this.$refs.fileInput.dispatchEvent(new Event('change', { bubbles: true }));
                // }
              
            });
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
      width: 100%; /* Adjust the maximum width to widen the modal */
      max-width: 1500px;
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
        overflow-y: auto;
      padding: 10px;
      max-height: 90vh;
      max-width: auto;
    }
  
    .input-field {
      background-color: #F3F4F6;
      border: 1px solid #D1D5DB;
      color: #111827;
      font-size: 14px;
      border-radius: 8px;
      padding: 10px;
      outline: none;
      width: 100%;
      transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    .input-field-select {
      background-color: #F3F4F6;
      border: 1px solid #D1D5DB;
      color: #111827;
      font-size: 14px;
      border-radius: 8px;
      padding: 10px;
      outline: none;
      width: fit-content;
      transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
  
  
    .close {
      cursor: pointer;
    }

    .resizable {
        resize: vertical; /* Allow vertical resizing only */
        min-height: 200px; /* Set a minimum height to prevent the textarea from becoming too small */
    }
    
  </style>