<template>
    <div class="modal" v-if="show">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit FAQ</h5>
                    <button type="button" class="close" @click="closeEditFaqModal">
                    <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form @submit.prevent="submitFaq">
                        <div class="mt-4">
                            <label for="questions" value="Questions" class="label">
                                Question
                            </label>
                            <textarea name="questions" id="questions" v-model="fields.question" cols="50" rows="10" class="!important mt-1 block w-full rounded-md textArea " required> </textarea>
                        </div>
                        <div class="mt-4">
                            <label for="answers" value="Answers" class="label">
                                Answer
                            </label>
                            <textarea name="answers" id="answers" v-model="fields.answer" class="mt-1 block w-full rounded-md textArea" required></textarea>
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
  import Swal from 'sweetalert2';
  
  export default {
    props: {
      show: Boolean,
      fields: Object,
    },

    methods: {
        initialData() {
            axios.get(`/faqlist`).then((response) => {
            this.faq_list = response.data;
            // Initially, display the complete Faq list
            this.filteredFaqList = this.faq_list;
            });
        },

        closeEditFaqModal() {
            this.$emit('close'); // Emit the 'close' event to close the modal
        },
    
        edit(id){
            axios.get(`/faq-data/${id}`).then(response=> {
                this.fields = response.data;
            });
        },
    
        clearFormFields() {
            this.fields = {};
        },

        submitFaq(){
            axios.post('/submit-edit-faq',this.fields).then((response)=>{
                if(response.data){
                    this.initialData();
                    // alert('success');
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
      width: 400px; /* Adjust the maximum width to widen the modal */
    }
  
    .modal-dialog {
      background-color: white;
      border-radius: 8px; /* Increase the border radius for rounded corners */
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      width: 400px; /* Increase the maximum width to widen the modal */
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
      text-align: left;
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
  </style>