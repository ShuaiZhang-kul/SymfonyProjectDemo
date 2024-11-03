function handleLogout() {
    // 假设你有一个登出的路由
    window.location.href = '/logout';
} 
function openNewCourseForm() {
    document.getElementById('newCourseModal').style.display = 'block';
  }
  
  function closeNewCourseForm() {
    document.getElementById('newCourseModal').style.display = 'none';
  }
  
  document.getElementById('newCourseForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const formData = {
      courseName: document.getElementById('courseName').value,
      courseCode: document.getElementById('courseCode').value,
      description: document.getElementById('description').value
    };
  
    try {
      const response = await fetch('/course/new', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(formData)
      });
  
      if (response.ok) {
        // 成功创建课程后刷新页面
        window.location.reload();
      } else {
        alert('创建课程失败，请重试');
      }
    } catch (error) {
      console.error('Error:', error);
      alert('发生错误，请重试');
    }
  });
  
  // 点击modal外部关闭
  window.onclick = function(event) {
    const modal = document.getElementById('newCourseModal');
    if (event.target == modal) {
      modal.style.display = "none";
    }
  }