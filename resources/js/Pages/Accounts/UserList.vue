<template>
    <!-- Start of the table -->
  <div class="max-w-9xl mx-auto">
    <div class=" overflow-hidden">
      <div class=" body">
        <div class="flex items-center justify-center">
          <p class="content-heading my-7">
            Accounts
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
              @input="filterUser"
            />
          </div>
          <div class="ml-40">
            <button @click="openModal" class="w-12 h-12 shadow  text-white hover:opacity-80 focus:shadow-outline focus:outline-none font-bold rounded-lg">
                <img src="/images/add.svg" alt="Delete" class="w-full">
            </button>
            <manage-acc-modal :show="showModal" @close="closeModal" @accountSaved="handleAccountSaved" :fields="fields"/>
          </div>
        </div>
        <div v-if="filteredUserList.length > 0" class="table-container">
          <table class="table" >
            <thead class="thead items-center">
              <th>#</th>
              <th>Name</th>
              <th>Email</th>
              <th>Role</th>
              <th>Access</th>
              <th>Status</th>
              <th>Action</th>
            </thead>
            <tbody>
              <tr v-for="(user, index) in filteredUserList.slice(0,5)" :key="index" class="row"  >
                <td style="width: 30px; text-align:center; padding: 15px;">{{ index + 1 }}</td>
                <td style="width: 500px; text-align:center; padding: 15px;">{{user.name}}</td>
                <td style="width: 500px; text-align:center; padding: 15px;">{{user.email}}</td>
                <td style="width: 100px; text-align:center; padding: 15px;">{{roleLabels[user.role]}}</td>
                <td style="width: 500px; text-align:center; padding: 15px;">
                  <ul class="no-bullet-list">
                    <li>{{ getUserAccessLabel(user, 'generic_pages') }}</li>
                    <li>{{ getUserAccessLabel(user, 'faq') }}</li>
                    <li>{{ getUserAccessLabel(user, 'articles') }}</li>
                    <li>{{ getUserAccessLabel(user, 'images') }}</li>
                    <li>{{ getUserAccessLabel(user, 'accounts') }}</li>
                  </ul>
                </td>
                <td style="width: 200px; text-align:center; padding: 15px;">
                  <!-- <label class="switch">
                    <input type="checkbox" v-model="user.status" @change="toggleUserStatus(user)">
                    <div class="slider round" :class="{ 'active-slider': user.status === 'deactive' }"></div>
                  </label> -->
                  <!-- <button class="buttonDelTable items-center shadow rounded-md h-9 w-auto m-2 px-4 text-white bg-red" :class="{ 'bg-red': user.status === 'deactive' }" @click="toggleUserStatus(user)">
                    {{ user.status === 0 ? 'ACTIVATE' : 'DEACTIVATE' }}
                  </button> -->
                  <button class="buttonDelTable items-center shadow rounded-md h-9 w-auto m-2 px-4 text-white bg-green  "
                          :class="{ 'bg-red': user.status === 1 }"
                          @click="toggleUserStatus(user)">
                      {{ user.status === 0 ? 'ACTIVATE' : 'DEACTIVATE' }}
                  </button>

                </td>
                <td style="width: 300px; text-align:center; padding: 15px;">
                  <button @click="edit(user.id)" >
                    <img src="/images/edit.svg" alt="" class="icon">
                  </button>

                  <button @click="editPass(user.id)" >
                    Change Password
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <edit-acc-modal :show="showEditAccModal" @close="closeEditAccModal" :fields="fields" @accountSaved="handleAccountSaved" @save="saveEditedData" />
        <change-password-modal :show="showChangePasswordModal" @close="closeChangePasswordModal" :fields="fields" @save="saveEditedData" />
        <p v-if="noMatchMessage" class="message">{{ noMatchMessage }}</p>
      </div>
    </div>
  </div>
    <!-- End of the table -->
</template>

<script>
  import ManageAccModal from '@/Components/AccountModal/ManageAccModal.vue';
  import EditAccModal from '@/Components/AccountModal/EditAccModal.vue';
  import ChangePasswordModal from '@/Components/AccountModal/ChangePasswordModal.vue';
  import axios from 'axios';
  
  export default {

    components: {
      ManageAccModal,
      EditAccModal,
      ChangePasswordModal,
    },

    data() {
      return {
        fields: {
          name: '',
          email: '',
          role: '',
          password: ''
        },
        user_list: [],
        searchQuery: "", 
        filteredUserList: [], 
        noMatchMessage: "",
        showModal: false,
        editingUserId: null,
        showEditAccModal: false,
        showChangePasswordModal: false,

        roleLabels: {
          1: 'Admin',
          2: 'Librarian',
        },

        accessLabels: {
          generic_pages: 'Generic Pages',
          faq: 'FAQs',
          articles: 'Articles',
          images: 'Images',
          accounts: 'Accounts',
        },

        data: {
          checkbox: false
        },
      };
    },

    computed: {
      
    },

    created() {
      this.initialData();
      this.fetchUserList();
    },

    methods: {
      openModal() {
        this.showModal = true;
        this.fields = {
          name: '',
          email: '',
          role: '',
        };
      },
      getUserAccessLabel(access, key) {
        // Assuming access is an object like { generic_pages: 1, faq: 0, ... }
        return access[key] === 1 ? this.accessLabels[key] : '';
      },

      closeModal() {
        this.initialData();
        this.showModal = false;
      },

      openChangePasswordModal() {
        this.showChangePasswordModal = true;
        this.fields = {
          password: '',
        };
      },

      closeChangePasswordModal() {
        this.showChangePasswordModal = false;
      },

      initialData() {
        axios.get(`/userlist`).then((response) => {
            this.user_list = response.data;

            // Fetch user access data
            this.user_list.forEach(async (user) => {
                await this.getUserAccess(user);
            });

            this.user_list.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));

            // Initially, display the complete User list
            this.filteredUserList = this.user_list;
        });
      },

      // async getUserAccess(user) {
      //     await axios.get(`/get-user-access/${user.id}`).then((response) => {
      //         user.access = response.data;
      //     });
      // },

      edit(id){
        axios.get(`/user-data/${id}`).then(response=> {
          this.openEditAccModal();
          this.fields = response.data;
        });
      },

      editPass(id){
        axios.get(`/user-data/${id}`).then(response=> {
          this.openChangePasswordModal();
          this.fields = response.data;
        });
      },

      openEditAccModal() {
        this.showEditAccModal = true;
      },

      closeEditAccModal() {
        this.initialData();
        this.showEditAccModal = false;
      },

      getUserStatus(user) {
        axios.get(`/get-user-status/${user.id}`).then((response) => {
          user.status = response.data.status;
        });
      },
 
      // Function to filter the user list based on the search query
      filterUser() {
        if (this.searchQuery) {
          // Filter the User list when a search query is entered
          this.filteredUserList = this.user_list.filter((user) => {
            const query = this.searchQuery.toLowerCase();
            const nameMatch = user.name.toLowerCase().includes(query);
            const emailMatch = user.email.toLowerCase().includes(query);
            const roleMatch = this.roleLabels[user.role].toLowerCase().includes(query);

            // Return true if any of the fields match the query
            return nameMatch || emailMatch || roleMatch ;
          });

          // Check if there are no matches and set the "no match" message
          if (this.filteredUserList.length === 0) {
            this.noMatchMessage = "No matches found.";
          } else {
            this.noMatchMessage = ""; // Clear the "no match" message
          }
        } else {
          // Display the complete User list when the query is empty
          this.filteredUserList = this.user_list;
          this.noMatchMessage = ""; // Clear the "no match" message
        }
      },

      toggleUserStatus(user) {
         const action = user.status === 'active' ? 'deactivate' : 'activate';
        if (confirm(`Are you sure to ${user.status === 1 ? 'deactivate' : 'activate'} this account?`)) {
          const action = user.status === 1 ? 'deactivate' : 'activate';
          axios.post(`/${action}-acc/${user.id}`).then((response) => {
            user.status = user.status === 'deactivate' ? 'active' : 'deactive';
            alert(`Account successfully ${action.charAt(0).toUpperCase() + action.slice(1)}d!`);
            this.initialData();
          }).catch((error) => {
            alert(`Error while ${action}ing: ${error}`);
          });
        } else {
          alert('Cancelled!');
        }
      },
    
      // toggleCheckbox() {
      //   this.checkbox = user.status === 1;

      //   this.$emit('setCheckboxVal', this.checkbox)
      // },

      fetchUserList() {
        axios.get('/userlist').then((response) => {
          this.user_list = response.data;
        });
      },

      handleAccountSaved() {
      // Refresh your user data after an account is saved
        this.fetchUserList();
      },
    }
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
</style>

<style scoped>
  #content1 {
    
    margin-top: 80px;
    background-color: #D9D9D9;
  }

  .icon{
    height: 24px;
    width: auto;
  }
</style>