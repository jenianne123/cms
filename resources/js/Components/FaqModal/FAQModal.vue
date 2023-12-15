<template>
    <div class="modal" v-if="show">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        Add FAQ
                    </h5>
                    <button type="button" class="close" @click="closeModal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form @submit.prevent="submitFaq">
                        <div class="mt-4">
                            <label for="questions" value="Questions" class="label">
                                Question
                            </label>
                            <textarea name="questions" id="question" v-model="fields.question" cols="50" rows="10" class="!important mt-1 block w-full rounded-md textArea " required> </textarea>
                        </div>
                        <div class="mt-4">
                            <label for="answer" value="Answer" class="label">
                                Answer
                            </label>
                            <textarea name="answers" id="answer" v-model="fields.answer" class="mt-1 block w-full rounded-md textArea" required></textarea>
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
    
    export default {
        props: {
            show: Boolean,
            fields: Object,
        },
    
        data() {
            return {
                fields: {
                    category:'',
                    type: '',
                    title: '',
                    description: '',
                    content: '',

                },
            };
        },
    
        methods: {
            closeModal() {
                this.$emit('close'); // Emit the 'close' event to close the modal
            },
        
            initialData() {
                axios.get(`/faqlist`).then((response) => {
                    this.faq_list = response.data;

                    this.faq_list.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
                    // Initially, display the complete FAQ list
                    this.filteredFaqList = this.faq_list;
                });
            },

            submitFaq(){
                axios.post('submit-form',this.fields).then((response)=>{
                    if(response.data){
                        this.initialData();
                        // alert('success');
                        this.fields = {};
                        alert('Added Successfully');
                        this.$emit('close');
                    }else{
                        alert('Error');
                    }
                });
            },
           
            clearFormFields() {
                this.fields = {};
            },

            edit(id){
                axios.get(`/faqs-data/${id}`).then(response=> {
                    this.fields = response.data;
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
      z-index: 9999;
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