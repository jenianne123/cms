<template>
    <div class="max-w-9xl mx-auto">
      <div class="overflow-hidden">
        <div class="body">
          <div class="flex items-center justify-center">
            <p class="content-heading my-7">
              Images
            </p>
          </div>
          <div class="flex justify-between items-center max-w-full">
            <div class="relative mb-4 flex items-center w-9/12">
              <input
                type="search"
                class="relative m-0 -mr-0.5 textArea block w-[1px] min-w-0 flex-auto rounded border bg-transparent bg-clip-padding px-3 py-[0.25rem] text-base font-normal leading-[1.6] text-neutral-700 outline-none transition duration-200 ease-in-out focus:z-[3] focus:border-primary focus:text-neutral-700 focus:shadow-[inset_0_0_0_1px_rgb(59,113,202)] focus:outline-none dark:border-neutral-600 dark:text-neutral-200 dark:placeholder:text-neutral-200 dark:focus:border-primary"
                placeholder="Search"
                aria-label="Search"
                aria-describedby="button-addon1"
                v-model="searchQuery"
                @input="filterImages"
              />
            </div>
            <div class="ml-40">
              <button @click="openModal" class="w-12 h-12 shadow text-white hover:opacity-80 focus:shadow-outline focus:outline-none font-bold rounded-lg">
                <img src="/images/add.svg" alt="Add" class="w-full">
              </button>
              <Image-modal :show="showModal" @close="closeModal" :fields="fields" @save="uploadImage" />
            </div>
          </div>
          <!-- Table for the images display -->
          <div v-if="filteredImageList.length > 0" class="table-container">
            <table class="table">
              <thead class="thead items-center">
                <!-- <th>#</th> -->
                <th>Image</th>
                <th>Filename</th>
                <th>Action</th>
              </thead>
              <tbody>
                <tr v-for="(image, index) in filteredImageList.slice(0,5)" :key="index" class="row">
                  \<!-- <td style="width: 30px; text-align:center; padding: 15px;">{{ index + 1 }}</td> -->
                  <td style="width: 600px; text-align: center; padding: 15px;">
                    <div style="display: flex; justify-content: center; align-items: center; height: 100%;">
                      <img v-if="image.url" :src="getImageUrl(image.filename)" alt="Image" class="image" />
                    </div>
                  </td>
                  <td style="width: 300px; text-align:center; padding: 15px;">
                    {{ image.orig_filename }}
                  </td>
                  <td style="text-align: center;">
                    <button @click="deleteImage(image.id)" class="ml-3">
                      <img src="/images/delete.svg" alt="Delete" class="icon">
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <p v-if="noMatchMessage" class="message">{{ noMatchMessage }}</p>
        </div>
      </div>
    </div>
</template>

<script>
import axios from 'axios';
import ImageModal from '@/Pages/AddImage/ImageModal.vue';
import Swal from "sweetalert2";

export default {
  components: {
    ImageModal,
  }, 

  data() {
    return {
      fields: {},
      image: {
        file: null,
      },
      previewImage: null,
      selectedImage: null,
      image_list: [],
      showModal: false,
      searchQuery: "",
      filteredImageList: [],
      noMatchMessage: "",

      
    };
  },

  created() {
    this.initialData();
    this.fetchImageList();
  },

  methods: {
    initialData() {
      axios.get('/imagelist').then((response) => {
        this.image_list = response.data;

        this.image_list.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));

        // Initially, display the complete FAQ list
        this.filteredImageList = this.image_list;
      });
    },

    getImageUrl(filename) {
      return `/storage/images/${filename}`;
    },

    onFileChange(event) {
      const file = event.target.files[0];
      if (file) {
        this.image.file = file;

        const reader = new FileReader();
        reader.onload = () => {
          this.previewImage = reader.result;
        };
        reader.readAsDataURL(file);
      }
    },

    uploadImage() {
      const formData = new FormData();
      formData.append('image', this.image.file);

      axios.post('/uploadimage', formData)
        .then((response) => {
          alert('success');
          this.$emit('close');
          this.image.file = null;
          this.fetchImageList();
        })
        .catch((error) => {
          alert('Error');
        }); 
    },

    reset() {
      this.image.file = null;
    },

    deleteImage(id) {
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

        axios.delete(`/delete-image/${id}`)
          .then((response) => {
            if (response.data) {
              Swal.fire({
                    icon: "success",
                    title: "Content Deleted Successfully!",
                }).then(() => {
                    this.$emit('close');
                });
                this.fetchImageList();
            } else{
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

    fetchImageList() {
      axios.get('/imagelist').then((response) => {
        this.image_list = response.data;
        this.filterImages(); // Call filterImages after fetching the image list
      });
    },

    openModal() {
      this.showModal = true;
      this.fields = {
        file: null, // Adjust the field based on your requirements
      };
    },

    closeModal() {
      this.initialData();
      this.showModal = false;
    },

    filterImages() {
      if (this.searchQuery) {
        this.filteredImageList = this.image_list.filter((image) => {
          const query = this.searchQuery.toLowerCase();
          const filenameMatch = image.orig_filename.toLowerCase().includes(query);
          return filenameMatch;
        });

        if (this.filteredImageList.length === 0) {
          this.noMatchMessage = "No matches found.";
        } else {
          this.noMatchMessage = "";
        }
      } else {
        this.filteredImageList = [...this.image_list];
        this.noMatchMessage = "";
      }
    },
  },
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
        margin-bottom: 40px;

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
        max-height: fit-content; 
        overflow-y: auto; 
        background-color: white;
        width: 100%;
        position: relative;
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

    .image{
      height: 100px;
      width: auto;
    }

    .icon{
      width: 40px;
      height: auto;
    }

</style>