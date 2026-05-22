import {useState} from 'react'
import './register.css'
import '../../App.css'
import {Link, useNavigate} from 'react-router-dom'
import Axios from 'axios'

const register = () =>{

  const [email, setEmail] = useState('');
  const [userName, setUserName] = useState('');
  const [userRegId, setRegId] = useState('');
  const [password, setPassword] = useState('');
  const navigateTo = useNavigate()

  const createUser = (e) =>{
      e.preventDefault();
      Axios.post('http://localhost:3000/register', {
      // creating objects of it
      Email: email,
      UserName: userName,
      Password: password,
      UserRegId: userRegId
    }).then(()=>{

        console.log('The user has Successfully Registered')
        setTimeout(() => {
          navigateTo('/')
        }, 200);

    //    navigateTo('/')
        
        //clear the fields
        setEmail('')
        setUserName('')
        setPassword('')
        setRegId('')
    })
  }

  return (
    <div>
      <h3>Register</h3>

      <form action="" className="">
        <span className='showMessage'> Login status will go here</span>

        <div className='inputdiv'>
          <label htmlFor="email"> Email</label>
            <div className='inputFlex'>
              {/*Also add icon here*/}
            <input type='email' id='email' placeholder='Enter email' onChange={(event) =>{
                setEmail(event.target.value)
              }
            }></input>
          </div>
        </div>

        <div className='inputdiv'>
          <label htmlFor="username"> Username</label>
            <div className='inputFlex'>
              {/*Also add icon here*/}
            <input type='text' id='username' placeholder='Enter username'  onChange={(event) => {
              setUserName(event.target.value)
            }}
            
            />
          </div>
        </div>

        <div className='inputdiv'>
          <label htmlFor="RegId"> RegId</label>
            <div className='inputFlex'>
              {/*Also add icon here*/}
            <input type='text' id='regId' placeholder='Enter RegId'  onChange={(event) => {
              setRegId(event.target.value)
            }}
            
            />
          </div>
        </div>

        <div className='inputdiv'>
          <label htmlFor="password"> Password</label>
            <div className='inputFlex'>
              {/*Also add icon here*/}
            <input type='password' id='password' placeholder='Enter password'
              onChange={(event) => {
                setPassword(event.target.value)
              }}
            />

          </div>
        </div>
  
        <span className='loginButton'>
          <button type='submit' className='loginButton' onClick={createUser} > Register </button>
        </span>

      </form>

      <br/>
      <Link to={'/'}>Already a member</Link>
    
    </div>
  )
}

export default register