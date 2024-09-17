import React from 'react'
import ReloadSvg from '../img/ReloadSvg'

const ErrorHandler = ({code, reloadHandler}) => {
   const errors = {
      400: "Пожалуйста перепроверьте введённые данные.",
      500: "Ошибка сервера. Обратитесь в поддержку.",
      300: "Ошибка прокси. Проверьте настройки прокси и повторите попытку.",
      '[?]': "Возникла непредвиденная ошибка. Проверьте подключение."
   }
   const errorCodeHandler = () => {
      if(errors[code]) return code;
      else if (code >= 400 && code < 500) return 400;
      else if (code>=500 && code < 600) return 500;
      else return "[?]";
   }

   const handleReloadBtn = () => {
      reloadHandler();
   }
   return (
      <div className='flex flex-col items-center'>
         <h1 className="text-red-500 text-9xl font-semibold mb-3">Error {errorCodeHandler()}</h1>
         <p className='text-center'>Ошибка при получении данных. <br/>
            {errors[errorCodeHandler()]}</p>
         <button 
            className='w-10 mt-1 rounded-full ring-2 ring-slate-700/25 
               hover:bg-gray-400/25 transition-all
               hover:-rotate-90 ' 
            onClick={handleReloadBtn}>
            <ReloadSvg
               className='fill-gray-700 m-1 -translate-y-[1px] ' />
         </button>
      </div>
   )
}

export default ErrorHandler