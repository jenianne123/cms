<template>
    <div class="modal" v-if="show">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        Edit Article
                    </h5>
                    <button type="button" class="close" @click="closeModal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form @submit.prevent="saveArticle">
                        <div class="mt-4">
                            <label for="title" value="title" class="label" >Title</label>
                            <input type="text" id="title" v-model="fields.title" class="mt-1 block w-full" required>
                        </div>

                        <div class="mt-4">
                            <label for="description" value="description" class="label">Description</label>
                            <textarea name="description" id="description" v-model="fields.description" class="mt-1 block w-full rounded-md textArea resizable" required></textarea>
                        </div>

                        <div class="mt-4">
                            <label for="type" value="Type" >Types</label>
                            <select id="type" v-model="fields.type" required>
                                <option value=1>Announcement</option>
                                <option value=2>News</option>
                                <option value=3>Articles</option>
                           </select>
                        </div>

                        <div class="mt-4" v-if="fields.type == 2 || fields.type == 3">
                            <label for="upload" value="image" >Upload Image</label>
                         
                            <input  type="file" accept="image/*" @change="uploadImage" class="mt-1 block w-full" >
 
                            <div v-if="previewImage">
                            <img :src="previewImage" alt="Existing Image" style="max-width: 200px; max-height: 200px;">
                
                            </div>
                            <div v-if="existingImage">
                                Existing Image:
                                <img :src="getImageUrl(existingImage, fields.type)" style="max-width: 200px; max-height: 200px;" alt="Existing Image">
                            </div>

                                <!-- <div v-else>
                                    {{imageShow.length}}
                                    <div v-if="imageShow.length" >
                                        <img :src="getImageUrl(imageShow.imageDetails.filename)" style="max-width: 200px; max-height: 200px" alt="uls">
                                    </div>
                                </div> -->
                        </div>

                        <div class="mt-4" >
                            <label for="contents" value="contents">Content</label> 
                            <ckeditor :editor="editor" :config="editorConfig" v-model="fields.contents"></ckeditor>
                           </div>

                        <div class="flex items-center justify-center mt-4">            
                            <button type="Submit" class="w-20 text-white shadow bg-green hover:opacity-70 focus:shadow-outline focus:outline-none font-bold py-2 px-4 rounded mx-2">
                                Submit
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
            existingImage: String,
        },
    
        data() {
            return {
             
          
                uploadedFilename: '',
                image: '', 
                showParent: false ,
                editor: ClassicEditor,
                editorData:  '<p></p>',
                editorConfig:{},
                // existingImage: '' ,
                previewImage: null,
                imageFile: null, // Initialize imageFile
                localExistingImage: '', 
            };
        },
    
        methods: {
            closeModal() {
                this.$emit('success'); // Emit the 'close' event to close the modal
            },

            async saveArticle() {
                try {
                    if (this.fields.type === 1) {
                    await this.saveArtDetails();
                    } else {
                    const imageFormData = new FormData();
                    imageFormData.append('image', this.imageFile);
                    imageFormData.append('type', this.fields.type);

                    if (this.fields.imageid) {
                        imageFormData.append('id', this.fields.imageid);
                    }

                    const response = await axios.post('upload-image', imageFormData);
                    if(response.data)
                        this.fields.imageid = response.data;
                        await this.saveArtDetails();
                    }

                    // alert('Success');
                    this.$emit('success');
                } catch (error) {
                    console.error('Error saving article:', error);
                    Swal.fire({
                        icon: "error",
                        title: "Error Editing Content",
                        text: response.data.error,
                    });
                }
            },

            async saveArtDetails() {
                const response = await axios.post('submit-article', this.fields);
                Swal.fire({
                    icon: "success",
                    title: "Successfully Edited!",
                }).then(() => {
                    this.initialData();
                });
            },

           
            clearFormFields() {
                this.fields = {};
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
       
        //     axios.get(`/article-data/${id}`).then(response=> {
        //         this.fields = response.data.details;
                
        //         this.existingImage = response.data.existingImage;
        //         // this.previewImage = this.getImageUrl(this.existingImage); 
        //         this.previewImage = this.getImageUrl(this.existingImage);
             

        //         // console.log('Image ID during edit:', this.fields.imageid); 
        //         console.log('Image filename during edit:', this.fields.filename);

        //         if (this.fields.contents === null) {
        //             this.fields.contents = response.data.contents;
        //         }

        //         // if(this.$refs.fileInput){
        //         //     this.$refs.fileInput.value = this.existingImage;
        //         //     this.$refs.fileInput.dispatchEvent(new Event('change', { bubbles: true }));
        //         // }
              
        //     });
        // },
        getImageUrl(filename, type){
            // return `/storage/news/${filename}`;
            return `/storage/${type === 2 ? 'news' : 'articles'}/${filename}`;
        },  
        },
        mounted() {
        this.editorData="";
        // if (this.fields.imageid) {
        // this.localExistingImage = this.getImageUrl(this.fields.filename);
        // this.imageFile = new File([], this.fields.filename);
        // }
        console.log('Existing Image:', this.localExistingImage);
        console.log('Component is mounted');
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
      z-index: 9999;
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
  
  
    .close {
      cursor: pointer;
    }

    .resizable {
        resize: vertical; /* Allow vertical resizing only */
        min-height: 200px; /* Set a minimum height to prevent the textarea from becoming too small */
    }
  </style>