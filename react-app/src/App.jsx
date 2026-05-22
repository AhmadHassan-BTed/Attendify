import './App.css'

import Portal from './Components/Portal/portal'
import Login from './Components/Login/login'
import Register from './Components/Register/register'

import {createBrowserRouter, RouterProvider} from 'react-router-dom'


const myrouter = createBrowserRouter([
    {
      path: '/',
      element: <div><Login/></div>
    }
    ,
    {
      path: '/register',
      element: <div><Register/></div>
    }
    ,
    {
      path: '/dashboard',
      element: <div><Portal/></div>
    }
])


function App() {

  return (
    <div>
      <RouterProvider router={myrouter}/>
    </div>
  )
}

export default App