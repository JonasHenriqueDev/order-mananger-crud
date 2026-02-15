import {createContext, useState} from "react";
import * as React from "react";

interface IAuthContextProps {
    email: string | undefined,
    accessToken: string | undefined,

    logout () : void;
    login (email: string, password: string) : void;
}

const AuthContext = createContext({} as IAuthContextProps);

export const AuthProvider = ({children} : React.PropsWithChildren) => {

    const [email, setEmail] = useState<string>();
    const [accessToken, setAccessToken] = useState<string>();


    const logout = () => {

    }
    const login = (email: string, password: string) => {}

    return (
        <AuthContext.Provider value={{login, logout, accessToken, email}}>
            {children}
        </AuthContext.Provider>
    );
}