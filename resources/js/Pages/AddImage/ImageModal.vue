<template>
    <div class="modal" v-if="show">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Upload Image</h5>
            <button type="button" class="close" @click="closeModal">
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="uploadImage">
              <div class="mt-4 items-center mb-6">
                <div class="mt-2" @dragover.prevent @drop="onDrop" @click="triggerFileInput">
                  <input
                    ref="fileInput"
                    accept="image/*"
                    @change="onFileChange"
                    class="!important w-full text-black bg-gray-200 appearance-none border-2 border-gray-200 rounded py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-purple-500"
                    id="image"
                    name="image"
                    type="file"
                    multiple
                    style="display: none;"
                  />
                  <div class="drop-area">
                    <span v-if="!previewImageUrls.length" >
                      Drag & Drop files here, or click to select files.
                    </span>
                    <div v-if="previewImageUrls.length > 0" class="flex">
                      <img v-for="(url, index) in previewImageUrls" :key="index" :src="url" alt="Preview" class="w-24 mr-2" />
                    </div>
                  </div>
                </div>
              </div>

              <div class="flex items-center justify-center mt-4">
                <button
                  type="submit"
                  class="buttonForm text-white w-28 shadow bg-green hover:opacity-80 mr-4 focus:shadow-outline focus:outline-none font-bold py-2 px-4 rounded"
                >
                  SAVE
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
</template>
  
<script>
    import axios from "axios";
import Swal from "sweetalert2";
    
    export default {
        props: {
        show: Boolean,
        fields: Object,
        },
    
        data() {
            return {
                fields: {},
                images: [], // Store an array of selected images
                previewImageUrls: [], // Store an array of preview image URLs
            };
        },

        methods: {
          resetPreviewImage() {
            this.previewImageUrls = [];// Reset the previewImage variable
            },
            onFileChange(event) {
                const files = event.target.files;

                if (files.length > 0) {
                    // Clear existing images and preview URLs when new files are selected
                    this.images = [];
                    this.previewImageUrls = [];

                    Promise.all(
                      Array.from(files).map((file) => this.readImage(file))
                    ).then(() => {
                      // After all images are read, set previewImage to true
                      this.$nextTick(() => {
                        this.previewImageUrls.length > 0;
                      });
                    });
                }
            },

            uploadImage() {
                // Check if files are selected
                if (!this.images.length) {
                    Swal.fire("Please select at least one image");
                    return;
                }

                // Create a FormData object to contain the images and other data.
                const formData = new FormData();
                this.images.forEach((image, index) => {
                    formData.append(`images[${index}]`, image, image.name);
                });

                // Send the FormData object to the Laravel backend using a POST request.
                axios
                .post("/uploadimage", formData)
                .then((response) => {
                  if(response.data){
                    Swal.fire({
                        icon: "success",
                        title: "Content is uploaded successfully!",
                    }).then(() => {
                      this.resetPreviewImage();
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

            reset() {
                // Clear selected images and preview URLs
                this.images = [];
                this.previewImageUrls = [];
            },

            closeModal() {
                this.$emit('close'); // Emit the 'close' event to close the modal
                this.resetPreviewImage();
            },

            readImage(file) {
              return new Promise((resolve) => {
                const reader = new FileReader();

                reader.onload = () => {
                  this.previewImageUrls.push(reader.result);
                  this.images.push(file);

                  resolve();
                };

                reader.readAsDataURL(file);
              });
            },

            triggerFileInput() {
              // When the drop area is clicked, trigger a click on the hidden file input
              this.$refs.fileInput.click();
            },

            onDrop(event) {
              // Prevent the default behavior of opening the file in the browser
              event.preventDefault();

              // Access the dropped files from the event dataTransfer
              const files = event.dataTransfer.files;

              // Call your existing file change method with the dropped files
              this.onFileChange({ target: { files } });
            },
        },  
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
      width: 700px; /* Adjust the maximum width to widen the modal */
      height: 500px;
    }
  
    .modal-dialog {
      background-color: white;
      border-radius: 8px; /* Increase the border radius for rounded corners */
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      width: 700px; /* Increase the maximum width to widen the modal */
      height: 500px;
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

    .drop-area {
      border: 2px dashed #ccc;
      padding: 20px;
      text-align: center;
      cursor: pointer;
      width: 100%;
      height: 300px;
      display: flex; 
      flex-direction: column; 
      align-items: center; 
      justify-content: center;
    }
</style>