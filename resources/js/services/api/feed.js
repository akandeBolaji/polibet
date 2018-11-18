
export default {
  addNewPost ( formData) {
    const config = {
      headers: {'content-type': 'multipart/form-data'}
    }
    return axios.post('/api/posts', formData, config)
    },

  getPosts () {
    return axios.get('/api/posts')
  },

  getPost (id) {
    return axios.get('/api/post' + id)
    .then(response => {
      //do anything
      return response.data
    })
  },

  createPost ( payload ) {
    return axios.post('/api/posts', payload)
    .then(response => {
      //do anything
      return response.data
    })
  }
}
