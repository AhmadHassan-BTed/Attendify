import {useEffect, useState} from 'react'
import './login.css'
import '../../App.css'
import {Link, useNavigate} from 'react-router-dom'
import Axios from 'axios'
import loginBackground from './login_background.png';
import universityIcon from './university_icon.png'; 
import googleLogo from './google_logo.png';


const login = () => {
  
  
  const [current_th_regId, set_current_th_regId] = useState([]);
  const [loginUserName, setLoginUserName] = useState('');
  const [loginPassword, setLoginPassword] = useState('');
  const navigateTo = useNavigate()

  // hold the status of the 
  const [loginStatus, setLoginStatus] = useState('');
  const [statusHolder, setStatusHolder] = useState('hideMessage');

  // for the even handler, so it will check the password for multiple false attempts
  const [attempt, setAttempt] = useState('')


  const loginUser = (e) =>{
    e.preventDefault();

    Axios.post('http://localhost:3000/login', { 
    LoginUserName: loginUserName,   // creating objects
    LoginPassword: loginPassword
  }).then((responce)=>{

      // if message is received, which is console.log(responce)
      // index.js:  75. 'res.send({message: 'Credentials Dont Match'})'
    
      if(responce.data.message || loginUserName == '' || loginPassword == ''){
        navigateTo('/')
        setAttempt(new Date().toISOString())
        setLoginStatus('Credentials Don\'t Exist')
        console.log('Credentials Don\'t Exist')
      }
      else{
        const th_regId = responce.data[0].th_regId;
        sessionStorage.setItem('isLoggedIn', 'true');
        sessionStorage.setItem('LoggedUserId', th_regId);
        navigateTo('/dashboard') // if credentails match
      }
  })

    // Always update loginStatus to trigger the useEffect
    setLoginStatus(loginStatus => loginStatus);
}

  useEffect(()=>{
      if(loginStatus !== ''){
        setStatusHolder('showMessage')
        setTimeout(() => {
          setStatusHolder('hideMessage')
        }, 4000);
      }
    }
    ,[attempt, loginStatus]
  )

  // // Check if user is already logged in
  // useEffect(() => {
  //   const isLoggedIn = localStorage.getItem('isLoggedIn');
  //   if (isLoggedIn) {
  //     navigateTo('/dashboard');
  //   }
  // }, []);

  const onSubmitClearTheForm = ()=>{
      setLoginUserName ('')
      setLoginPassword('')
   }

  // return (
  //   <div>
  //     <h3>Login</h3>
  //     <br/>

  //     <form action="" className="" onSubmit={onSubmitClearTheForm}>
  //       <span className={statusHolder}> {loginStatus}</span>

  //       <div className='inputdiv'>
  //         <label htmlFor="username"> Username</label>
  //           <div className='inputFlex'>
  //             {/*Also add icon here*/}
  //           <input type='text' id='username' placeholder='Enter username' onChange={ (event)=>{
  //             setLoginUserName(event.target.value)
  //           }}/>
  //         </div>
  //       </div>

  //       <div className='inputdiv'>
  //         <label htmlFor="password"> Password</label>
  //           <div className='inputFlex'>
  //             {/*Also add icon here*/}
  //           <input type='password' id='password' placeholder='Enter pass' onChange={(event) => {
  //             setLoginPassword(event.target.value)
  //           }}/>
  //         </div>
  //       </div>

  //       <div className='rememberMeDiv'>
  //         <input type="checkbox" id="rememberMe" name="rememberMe" />
  //         <label htmlFor="rememberMe">Remember Me</label>
  //       </div>

  //       <span className='forgetPassword'>
  //         <Link to={'/'}>Forget Password?</Link>
  //       </span>

  //       <span className='loginButton'>
  //         <button type='submit' className='loginButton' onClick={loginUser}>
  //           Login
  //         </button>
  //       </span>

  //     </form>

  //     <br/>
  //     Dont have an account? <Link to={'/register'}>Sign up here </Link>
  //   </div>
  // )

  return (
    <div>
       <div className="background-image-div">
         <img className='background-image' src={loginBackground} alt="backgroundpic"/>
       </div>
       <div className="login-container">
         <div className="grey-box">
           <div className="login-content">
             <div className='one'>
             <h1 className="login-heading">Login</h1>
             <h5 className="au-heading">Welcome to AU Attendance Portal</h5>
             </div>
             <span className={statusHolder}>{loginStatus}</span>   
         { 
             <div className='form'>
               <form action="" className="login-form" onSubmit={onSubmitClearTheForm}>
                 <div className='fields'>
                       <input type="text" id="username" className="login-input1" placeholder="   Email" onChange={(event) => {setLoginUserName(event.target.value)}}/>
                       <input type="password" id="password" className="login-input2" placeholder="   Password" onChange={(event) => { setLoginPassword(event.target.value)}}/>
                 </div>
                 <div className='fieldBottomLines'>
                       {/* <div className="rememberMeDiv">
                         <input type="checkbox" id="rememberMe" name="rememberMe" className="remember-me-checkbox" />
                         <label htmlFor="rememberMe" className="remember-me-label"></label>
                         <label htmlFor="rememberMe" className="remember-me-text">Remember Me</label>
                       </div> */}

                       <div className="rememberMeDiv">
                          <input type="checkbox" id="rememberMe" name="rememberMe" className="remember-me-checkbox" />
                          <label htmlFor="rememberMe" className="remember-me-label"></label>
                          <label htmlFor="rememberMe" className="remember-me-text">Remember me</label>
                      </div>

                       <a to={'/'} className="forget-password-link">Forget Password?</a>
                 </div>
                 <div className="login-section">
                         <div className='login-button-section'>
                           <button type="submit" className="login-button" onClick={loginUser}> Login </button>
                         </div>
                         <h5 className="signin-heading">or sign in with</h5>
                 </div>
               </form>
             </div>}
             <div className='last'>
               <div className='loginOptions'>
               <button href="#" className="google-logo-button">
               <img src={googleLogo} alt="Google Logo" className="image-google" />
               </button>
               <button href="#" className="uni-logo-button">
               <img src={universityIcon} alt="University Logo" className="image-uni" />
               </button> 
               </div> 
               <div className='lastBox'>
                 <p className="last-heading"> Dont have an account?</p>
                  <Link to={'/register'} className="signup"> <span style={{ fontWeight: 700, textDecoration: 'underline' }}> Sign up </span> here </Link>
              </div>
             </div> 
           </div> 
         </div>
       </div>
     </div>
   )
}

export default login